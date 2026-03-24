<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMarketingCampaignRequest;
use App\Services\Marketing\InactivePatientCampaignService;
use App\Services\Marketing\MarketingCampaignSender;
use App\Support\ClinicaActual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function __construct(
        private readonly InactivePatientCampaignService $campaignService,
        private readonly MarketingCampaignSender $campaignSender,
    ) {
    }

    public function index(Request $request): View
    {
        $campaignTypes = $this->campaignService->campaignTypes();
        $campaignType = $request->string('campaign_type')->toString() ?: InactivePatientCampaignService::CAMPAIGN_CONSULTATION;

        if (! array_key_exists($campaignType, $campaignTypes)) {
            $campaignType = InactivePatientCampaignService::CAMPAIGN_CONSULTATION;
        }

        $filters = [
            'q' => $request->string('q')->toString(),
            'include_never' => $request->boolean('include_never'),
        ];

        $recipients = $this->campaignService->paginateRecipients($campaignType, $filters);
        $counts = $this->campaignService->countRecipientsByCampaign($filters);
        $clinic = ClinicaActual::get();
        $clinicName = $clinic->name ?? $clinic->nombre ?? 'la clínica';
        $messageTemplate = old('message_template', $this->campaignService->defaultTemplate($campaignType));
        $previewRecipient = $recipients->getCollection()->first();
        $previewMessage = $previewRecipient
            ? $this->campaignService->renderMessage($messageTemplate, $previewRecipient, $clinicName)
            : strtr($messageTemplate, [
                '{owner_name}' => 'Tutor ejemplo',
                '{patient_name}' => 'Paciente ejemplo',
                '{last_consultation_date}' => 'Nunca',
                '{last_grooming_date}' => 'Nunca',
                '{clinic_name}' => $clinicName,
            ]);

        return view('marketing.index', [
            'campaignType' => $campaignType,
            'campaignTypes' => $campaignTypes,
            'filters' => $filters,
            'counts' => $counts,
            'recipients' => $recipients,
            'messageTemplate' => $messageTemplate,
            'previewMessage' => $previewMessage,
            'placeholders' => ['{owner_name}', '{patient_name}', '{last_consultation_date}', '{last_grooming_date}', '{clinic_name}'],
            'recentLogs' => $this->campaignService->recentLogs(),
            'clinicName' => $clinicName,
            'inspectedTables' => [
                'consultation' => 'historias_clinicas',
                'grooming' => 'groomings',
            ],
        ]);
    }

    public function send(SendMarketingCampaignRequest $request): RedirectResponse
    {
        $campaignType = $request->string('campaign_type')->toString();
        $recipients = $this->campaignService->selectedRecipients($campaignType, $request->input('patient_ids', []));

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Los pacientes seleccionados ya no están disponibles para esta campaña.');
        }

        $clinic = ClinicaActual::get();
        $clinicName = $clinic->name ?? $clinic->nombre ?? 'la clínica';
        $results = $this->campaignSender->send(
            $campaignType,
            $recipients,
            $request->string('message_template')->toString(),
            $clinicName,
            $request->user()
        );

        $messages = [];

        if ($results['sent'] > 0) {
            $messages[] = "{$results['sent']} enviados";
        }

        if ($results['stubbed'] > 0) {
            $messages[] = "{$results['stubbed']} registrados en stub";
        }

        if ($results['failed'] > 0) {
            $messages[] = "{$results['failed']} con error";
        }

        return redirect()
            ->route('marketing.index', ['campaign_type' => $campaignType])
            ->with('status', 'Resultado de campaña: ' . implode(', ', $messages) . '.');
    }
}
