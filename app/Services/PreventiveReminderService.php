<?php

namespace App\Services;

use App\Models\PatientDeworming;
use App\Models\PatientImmunization;
use Illuminate\Support\Collection;

class PreventiveReminderService
{
    public function upcomingForTenant(int $days = 7): Collection
    {
        $vaccines = PatientImmunization::query()
            ->nextDueWithin($days)
            ->get()
            ->map(fn ($record) => [
                'type' => 'vaccine',
                'patient_id' => $record->paciente_id,
                'next_due_at' => $record->next_due_at,
                'label' => $record->vaccine_name,
            ]);

        $dewormings = PatientDeworming::query()
            ->nextDueWithin($days)
            ->get()
            ->map(fn ($record) => [
                'type' => $record->type,
                'patient_id' => $record->paciente_id,
                'next_due_at' => $record->next_due_at,
                'label' => $record->item_manual ?? optional($record->item)->nombre,
            ]);

        return $vaccines->merge($dewormings)->sortBy('next_due_at')->values();
    }
}
