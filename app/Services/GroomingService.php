<?php

namespace App\Services;

use App\Models\Grooming;
use App\Models\GroomingReport;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class GroomingService
{
    public function createGrooming(array $data): Grooming
    {
        return DB::transaction(function () use ($data) {
            $grooming = new Grooming($data);
            $grooming->status = Grooming::STATUS_AGENDADO;

            if (! $grooming->owner_id && $grooming->patient) {
                $grooming->owner_id = $grooming->patient->owner_id;
            }

            if ($grooming->service_source === 'product' && $grooming->product_service_id && empty($grooming->service_price)) {
                $grooming->service_price = optional(Product::find($grooming->product_service_id))->sale_price;
            }

            $grooming->save();

            return $grooming;
        });
    }

    public function startGrooming(Grooming $grooming): Grooming
    {
        if ($grooming->status !== Grooming::STATUS_AGENDADO) {
            throw new InvalidArgumentException('Solo se puede iniciar un grooming agendado.');
        }

        $grooming->status = Grooming::STATUS_EN_PROCESO;
        $grooming->started_at = $grooming->started_at ?: Carbon::now();
        $grooming->save();

        return $grooming;
    }

    public function cancelGrooming(Grooming $grooming): Grooming
    {
        if ($grooming->status === Grooming::STATUS_FINALIZADO) {
            throw new InvalidArgumentException('No se puede cancelar un grooming finalizado.');
        }

        $grooming->status = Grooming::STATUS_CANCELADO;
        $grooming->save();

        return $grooming;
    }

    public function finalizeWithReport(Grooming $grooming, array $reportData, int $userId): Grooming
    {
        return DB::transaction(function () use ($grooming, $reportData, $userId) {
            if (! in_array($grooming->status, [Grooming::STATUS_AGENDADO, Grooming::STATUS_EN_PROCESO])) {
                throw new InvalidArgumentException('No se puede finalizar este grooming.');
            }

            $reportData['created_by'] = $userId;
            $reportData['created_at'] = $reportData['created_at'] ?? Carbon::now();

            GroomingReport::updateOrCreate(
                ['grooming_id' => $grooming->id],
                $reportData
            );

            $grooming->status = Grooming::STATUS_FINALIZADO;
            $grooming->finished_at = Carbon::now();

            if (! $grooming->started_at) {
                $grooming->started_at = $grooming->finished_at;
            }

            $grooming->save();

            return $grooming;
        });
    }
}
