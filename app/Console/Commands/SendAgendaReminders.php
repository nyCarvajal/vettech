<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use App\Services\WhatsApp\OneMsgClient;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendAgendaReminders extends Command
{
    protected $signature = 'agenda:send-reminders';

    protected $description = 'Envía recordatorios de citas por WhatsApp (día anterior y 1 hora antes).';

    public function handle(OneMsgClient $client): int
    {
        $windowMinutes = (int) config('services.onemsg.reminder_window_minutes', 10);
        $now = now();

        $dayStart = $now->copy()->addDay();
        $dayEnd = $dayStart->copy()->addMinutes($windowMinutes);
        $hourStart = $now->copy()->addHour();
        $hourEnd = $hourStart->copy()->addMinutes($windowMinutes);

        $this->sendReminders(
            $client,
            $this->queryReservas($dayStart, $dayEnd, 'reminder_day_before_sent_at'),
            fn (Reserva $reserva) => $this->buildDayBeforeMessage($reserva),
            'reminder_day_before_sent_at'
        );

        $this->sendReminders(
            $client,
            $this->queryReservas($hourStart, $hourEnd, 'reminder_hour_before_sent_at'),
            fn (Reserva $reserva) => $this->buildHourBeforeMessage($reserva),
            'reminder_hour_before_sent_at'
        );

        return Command::SUCCESS;
    }

    private function queryReservas(Carbon $start, Carbon $end, string $sentColumn)
    {
        return Reserva::with(['paciente.owner', 'tipocita'])
            ->whereBetween('fecha', [$start, $end])
            ->whereNull($sentColumn)
            ->whereNotIn('estado', ['Cancelada', 'Finalizada', 'No asistió', 'No Asistió'])
            ->get();
    }

    private function sendReminders(OneMsgClient $client, $reservas, callable $messageBuilder, string $sentColumn): void
    {
        foreach ($reservas as $reserva) {
            $phone = $this->resolvePhone($reserva);

            if (! $phone) {
                continue;
            }

            $client->sendMessage($phone, $messageBuilder($reserva));

            $reserva->forceFill([$sentColumn => now()])->save();
        }
    }

    private function resolvePhone(Reserva $reserva): ?string
    {
        $paciente = $reserva->paciente;
        $owner = $paciente?->owner;

        return $paciente?->whatsapp
            ?: $owner?->whatsapp
            ?: $owner?->phone
            ?: $paciente?->acompanante_contacto;
    }

    private function buildDayBeforeMessage(Reserva $reserva): string
    {
        $fecha = optional($reserva->fecha)->format('d/m/Y');
        $hora = optional($reserva->fecha)->format('H:i');
        $paciente = trim(($reserva->paciente?->nombres ?? '') . ' ' . ($reserva->paciente?->apellidos ?? '')) ?: 'tu mascota';
        $tipo = $reserva->tipocita?->nombre ?? $reserva->tipo ?? 'cita';
        $appName = config('app.name', 'VetTech');

        return "Hola, te recordamos la cita de {$paciente} ({$tipo}) para mañana {$fecha} a las {$hora}.\n{$appName}";
    }

    private function buildHourBeforeMessage(Reserva $reserva): string
    {
        $fecha = optional($reserva->fecha)->format('d/m/Y');
        $hora = optional($reserva->fecha)->format('H:i');
        $paciente = trim(($reserva->paciente?->nombres ?? '') . ' ' . ($reserva->paciente?->apellidos ?? '')) ?: 'tu mascota';
        $tipo = $reserva->tipocita?->nombre ?? $reserva->tipo ?? 'cita';
        $appName = config('app.name', 'VetTech');

        return "Tu cita de {$paciente} ({$tipo}) es hoy {$fecha} a las {$hora} (en 1 hora).\n{$appName}";
    }
}
