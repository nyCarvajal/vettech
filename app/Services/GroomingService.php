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

            $selectedServiceId = $data['service_id'] ?? $data['product_service_id'] ?? null;

            if ($selectedServiceId) {
                $product = Product::find($selectedServiceId);

                if ($product) {
                    $grooming->service_source = 'product';
                    $grooming->service_price = $product->sale_price;
                    $grooming->product_service_id = $selectedServiceId;
                    $grooming->service_id = null;
                } else {
                    $item = DB::table('Items')
                        ->where('id', $selectedServiceId)
                        ->first(['id', 'nombre', 'valor']);

                    if ($item) {
                        $grooming->service_source = 'item';
                        $grooming->service_price = $item->valor;
                        $grooming->service_id = $item->id;
                    } else {
                        $grooming->service_source = 'none';
                        $grooming->service_price = null;
                        $grooming->service_id = null;
                    }

                    $grooming->product_service_id = null;
                }
            } else {
                $grooming->service_source = 'none';
                $grooming->service_price = null;
                $grooming->service_id = null;
                $grooming->product_service_id = null;
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
