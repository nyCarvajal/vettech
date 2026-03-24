<?php

namespace App\Services\Marketing;

use App\Models\MarketingCampaignLog;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InactivePatientCampaignService
{
    public const CAMPAIGN_CONSULTATION = 'consultation';
    public const CAMPAIGN_GROOMING = 'grooming';

    public function campaignTypes(): array
    {
        return [
            self::CAMPAIGN_CONSULTATION => 'Campaña de consulta',
            self::CAMPAIGN_GROOMING => 'Campaña de peluquería',
        ];
    }

    public function paginateRecipients(string $campaignType, array $filters): LengthAwarePaginator
    {
        $query = $this->campaignQuery($campaignType, $filters)
            ->orderByRaw($this->lastActivityColumn($campaignType) . ' is null desc')
            ->orderBy($this->lastActivityColumn($campaignType))
            ->orderBy('patients.nombres')
            ->selectRaw($this->daysSinceExpression($campaignType) . ' as days_since_last_visit');

        $paginator = $query->paginate(20)->withQueryString();

        $paginator->getCollection()->transform(function ($recipient) use ($campaignType) {
            $recipient->campaign_type = $campaignType;
            $recipient->campaign_label = $this->campaignTypes()[$campaignType] ?? $campaignType;
            $recipient->patient_name = trim(($recipient->patient_first_name ?? '') . ' ' . ($recipient->patient_last_name ?? ''));
            $recipient->contact_phone = $recipient->owner_whatsapp ?: $recipient->owner_phone;

            return $recipient;
        });

        return $paginator;
    }

    public function countRecipientsByCampaign(array $filters): array
    {
        $counts = [];

        foreach (array_keys($this->campaignTypes()) as $campaignType) {
            $counts[$campaignType] = (clone $this->campaignQuery($campaignType, $filters))->count();
        }

        return $counts;
    }

    public function selectedRecipients(string $campaignType, array $patientIds): Collection
    {
        return $this->baseQuery()
            ->whereIn('patients.id', $patientIds)
            ->where($this->lastActivityEligibility($campaignType, true))
            ->orderBy('patients.nombres')
            ->selectRaw($this->daysSinceExpression($campaignType) . ' as days_since_last_visit')
            ->get()
            ->map(function ($recipient) use ($campaignType) {
                $recipient->campaign_type = $campaignType;
                $recipient->campaign_label = $this->campaignTypes()[$campaignType] ?? $campaignType;
                $recipient->patient_name = trim(($recipient->patient_first_name ?? '') . ' ' . ($recipient->patient_last_name ?? ''));
                $recipient->contact_phone = $recipient->owner_whatsapp ?: $recipient->owner_phone;

                return $recipient;
            });
    }

    public function recentLogs(int $limit = 15): Collection
    {
        return MarketingCampaignLog::query()
            ->with(['patient', 'owner', 'creator'])
            ->latest('sent_at')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function defaultTemplate(string $campaignType): string
    {
        return match ($campaignType) {
            self::CAMPAIGN_GROOMING => 'Hola {owner_name}, te escribimos de {clinic_name}. {patient_name} no visita peluquería desde {last_grooming_date}. ¿Quieres agendar su próxima cita?',
            default => 'Hola {owner_name}, te escribimos de {clinic_name}. {patient_name} no nos visita a consulta desde {last_consultation_date}. ¿Deseas programar un control?',
        };
    }

    public function renderMessage(string $template, object $recipient, string $clinicName): string
    {
        $replacements = [
            '{owner_name}' => $recipient->owner_name,
            '{patient_name}' => $recipient->patient_name,
            '{last_consultation_date}' => $this->formatDatePlaceholder($recipient->last_consultation_at),
            '{last_grooming_date}' => $this->formatDatePlaceholder($recipient->last_grooming_at),
            '{clinic_name}' => $clinicName,
        ];

        return strtr($template, $replacements);
    }

    private function baseQuery(): Builder
    {
        // Fuente real de consultas inspeccionada en el proyecto: historias_clinicas.
        $consultations = DB::table('historias_clinicas')
            ->selectRaw('paciente_id, MAX(created_at) as last_consultation_at')
            ->groupBy('paciente_id');

        // Fuente real de peluquería inspeccionada en el proyecto: groomings.
        $groomings = DB::table('groomings')
            ->where('status', 'finalizado')
            ->selectRaw('patient_id, MAX(COALESCE(finished_at, scheduled_at)) as last_grooming_at')
            ->groupBy('patient_id');

        $primaryTutors = DB::table('patient_owner')
            ->selectRaw('patient_id, MIN(owner_id) as owner_id')
            ->where('is_primary', true)
            ->groupBy('patient_id');

        return Patient::query()
            ->from('pacientes as patients')
            ->leftJoinSub($primaryTutors, 'primary_tutors', function ($join) {
                $join->on('primary_tutors.patient_id', '=', 'patients.id');
            })
            ->join('owners', function ($join) {
                $join->on('owners.id', '=', DB::raw('COALESCE(primary_tutors.owner_id, patients.owner_id)'));
            })
            ->leftJoinSub($consultations, 'consultations', function ($join) {
                $join->on('consultations.paciente_id', '=', 'patients.id');
            })
            ->leftJoinSub($groomings, 'groomings_summary', function ($join) {
                $join->on('groomings_summary.patient_id', '=', 'patients.id');
            })
            ->where('patients.activo', 1)
            ->where(function ($query) {
                $query->whereRaw("NULLIF(TRIM(COALESCE(owners.phone, '')), '') IS NOT NULL")
                    ->orWhereRaw("NULLIF(TRIM(COALESCE(owners.whatsapp, '')), '') IS NOT NULL");
            })
            ->select([
                'patients.id as patient_id',
                'patients.owner_id as fallback_owner_id',
                'patients.nombres as patient_first_name',
                'patients.apellidos as patient_last_name',
                'owners.id as owner_id',
                'owners.name as owner_name',
                'owners.phone as owner_phone',
                'owners.whatsapp as owner_whatsapp',
                'consultations.last_consultation_at',
                'groomings_summary.last_grooming_at',
            ]);
    }

    private function campaignQuery(string $campaignType, array $filters): Builder
    {
        $query = $this->baseQuery();
        $includeNever = (bool) ($filters['include_never'] ?? false);

        $query->where($this->lastActivityEligibility($campaignType, $includeNever));

        if (! empty($filters['q'])) {
            $search = trim((string) $filters['q']);

            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('patients.nombres', 'like', "%{$search}%")
                    ->orWhere('patients.apellidos', 'like', "%{$search}%")
                    ->orWhere('owners.name', 'like', "%{$search}%")
                    ->orWhere('owners.phone', 'like', "%{$search}%")
                    ->orWhere('owners.whatsapp', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function lastActivityEligibility(string $campaignType, bool $includeNever): \Closure
    {
        $column = $this->lastActivityColumn($campaignType);
        $cutoff = now()->subMonths(2)->toDateTimeString();

        return function ($query) use ($column, $includeNever, $cutoff) {
            $query->where(function ($activityQuery) use ($column, $cutoff, $includeNever) {
                $activityQuery->where($column, '<=', $cutoff);

                if ($includeNever) {
                    $activityQuery->orWhereNull($column);
                }
            });
        };
    }

    private function lastActivityColumn(string $campaignType): string
    {
        return match ($campaignType) {
            self::CAMPAIGN_GROOMING => 'groomings_summary.last_grooming_at',
            default => 'consultations.last_consultation_at',
        };
    }

    private function daysSinceExpression(string $campaignType): string
    {
        $column = $this->lastActivityColumn($campaignType);

        return "CASE WHEN {$column} IS NULL THEN NULL ELSE DATEDIFF(CURDATE(), DATE({$column})) END";
    }

    private function formatDatePlaceholder(mixed $value): string
    {
        if (blank($value)) {
            return 'Nunca';
        }

        return Carbon::parse($value)->format('d/m/Y');
    }
}
