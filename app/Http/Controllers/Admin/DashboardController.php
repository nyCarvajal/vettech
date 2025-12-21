<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('auth.signin');
        }

        $timezone = 'America/Bogota';
        $now = now($timezone);
        $now->setTimezone($timezone);
        $today = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();
        $windowEnd = $now->copy()->addHours(5);
        $windowEnd = $windowEnd->greaterThan($endOfDay)
            ? $endOfDay
            : $windowEnd;

        $totalClientes = Cliente::count();

        $reservasHoy = Reserva::with('entrenador')
            ->whereBetween('fecha', [$today, $endOfDay])
            ->get();

        $confirmadasHoy = $reservasHoy->where('estado', 'Confirmada')->count();
        $pendientesHoy = $reservasHoy->where('estado', 'Pendiente')->count();
        $canceladasHoy = $reservasHoy->where('estado', 'Cancelada')->count();
        $noAsistidasHoy = $reservasHoy->where('estado', 'No Asistida')->count();
        $totalAgendadasHoy = $reservasHoy->count();

        $baseAsistencia = $confirmadasHoy + $pendientesHoy + $canceladasHoy;
        $asistenciaPorcentaje = $baseAsistencia > 0
            ? round(($confirmadasHoy / max($baseAsistencia, 1)) * 100)
            : 100;

        $ausenciasRecuperadas = Reserva::whereDate('fecha', $today)
            ->whereDate('updated_at', $today)
            ->whereColumn('updated_at', '>', 'created_at')
            ->where('estado', 'Confirmada')
            ->count();

        $totalReservas = Reserva::where('estado', '!=', 'Cancelada')
            ->whereMonth('fecha', $now->month)
            ->whereYear('fecha', $now->year)
            ->count();

        $totalClases = Reserva::where('tipo', 'Clase')
            ->where('estado', '!=', 'Cancelada')
            ->whereMonth('fecha', $now->month)
            ->whereYear('fecha', $now->year)
            ->count();

        $clientes = Cliente::latest()->take(10)->get();

        $huecos = $this->calcularHuecosDisponibles($reservasHoy, $now, $windowEnd);
        $huecosDestacados = $huecos->take(3);
        $totalHuecosDisponibles = $huecos->count();

        $fechaHoyLegible = $now->copy()->setTimezone($timezone)->locale('es')
            ->translatedFormat('l d \d\e F');

        return view('admin.index', [
            'totalReservas' => $totalReservas,
            'totalClases' => $totalClases,
            'totalClientes' => $totalClientes,
            'clientes' => $clientes,
            'asistenciaPorcentaje' => $asistenciaPorcentaje,
            'ausenciasRecuperadas' => $ausenciasRecuperadas,
            'ausenciasHoy' => $noAsistidasHoy,
            'totalAgendadasHoy' => $totalAgendadasHoy,
            'confirmadasHoy' => $confirmadasHoy,
            'totalHuecosDisponibles' => $totalHuecosDisponibles,
            'huecosDestacados' => $huecosDestacados,
            'fechaHoyLegible' => $fechaHoyLegible,
        ]);
    }

    private function calcularHuecosDisponibles(Collection $reservasHoy, Carbon $windowStart, Carbon $windowEnd): Collection
    {
        $slots = collect();

        $reservasOrdenadas = $reservasHoy
            ->filter(fn ($reserva) => ! empty($reserva->fecha))
            ->sortBy('fecha')
            ->values();

        $cursor = $windowStart->copy();

        foreach ($reservasOrdenadas as $reserva) {
            $inicio = Carbon::parse($reserva->fecha, $windowStart->getTimezone())
                ->setTimezone($windowStart->getTimezone());
            $duracion = (int) ($reserva->duracion ?? 30);
            if ($duracion <= 0) {
                $duracion = 30;
            }
            $fin = $inicio->copy()->addMinutes($duracion);

            if ($fin->lessThanOrEqualTo($windowStart)) {
                continue;
            }

            if ($inicio->greaterThanOrEqualTo($windowEnd)) {
                break;
            }

            if ($inicio->greaterThan($cursor)) {
                $inicioHueco = $cursor->copy();
                $finHueco = $inicio->copy()->min($windowEnd);
                $duracionHueco = $inicioHueco->diffInMinutes($finHueco);

                if ($duracionHueco >= 30) {
                    $slots->push((object) [
                        'inicio' => $inicioHueco,
                        'duracion' => min($duracionHueco, 60),
                        'barbero' => $this->obtenerNombreBarbero($reserva->entrenador),
                        'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                    ]);
                }
            }

            if ($fin->greaterThan($cursor)) {
                $cursor = $fin->copy();
            }

            if ($cursor->greaterThanOrEqualTo($windowEnd)) {
                break;
            }
        }

        if ($cursor->lessThan($windowEnd)) {
            $duracionHueco = $cursor->diffInMinutes($windowEnd);

            if ($duracionHueco >= 30) {
                $ultimoBarbero = optional($reservasOrdenadas->last())->entrenador;

                $slots->push((object) [
                    'inicio' => $cursor->copy(),
                    'duracion' => min($duracionHueco, 60),
                    'barbero' => $this->obtenerNombreBarbero($ultimoBarbero),
                    'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                ]);
            }
        }

        if ($slots->isEmpty()) {
            $duracionHueco = $windowStart->diffInMinutes($windowEnd);

            if ($duracionHueco >= 30) {
                $slots->push((object) [
                    'inicio' => $windowStart->copy(),
                    'duracion' => min($duracionHueco, 60),
                    'barbero' => 'Equipo',
                    'servicio' => $this->sugerirServicioPorDuracion($duracionHueco),
                ]);
            }
        }

        return $slots->sortBy('inicio')->values();
    }

    private function obtenerNombreBarbero($barbero = null): string
    {
        if (! $barbero) {
            return 'Equipo';
        }

        $nombre = $barbero->nombre_completo
            ?? $barbero->nombre
            ?? $barbero->nombres
            ?? null;

        return $nombre ? trim($nombre) : 'Equipo';
    }

    private function sugerirServicioPorDuracion(int $minutos): string
    {
        if ($minutos <= 35) {
            return 'Corte rápido';
        }

        if ($minutos <= 55) {
            return 'Corte + Barba';
        }

        return 'Color / Tinte';
    }
}
