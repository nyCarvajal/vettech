<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Reserva;
use App\Models\Tipocita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ReservaController extends Controller
{
    public function index()
    {
        return Redirect::route('reservas.calendar');
    }

    public function calendar(Request $request)
    {
        $pacientes = Paciente::orderBy('nombres')->get();
        $medicos = User::orderBy('name')->get();
        $tipos = Tipocita::orderBy('nombre')->get();

        $selectedDate = Carbon::parse($request->input('fecha', now()->toDateString()))->startOfDay();
        $agenda = Reserva::with(['paciente', 'entrenador', 'tipocita'])
            ->whereDate('fecha', $selectedDate->toDateString())
            ->orderBy('fecha')
            ->get();

        $stats = [
            'total' => $agenda->count(),
            'atendidas' => $agenda->whereIn('estado', ['En curso', 'Finalizada', 'No Asistió'])->count(),
            'pendientes' => $agenda->where('estado', 'Pendiente')->count(),
        ];

        return view('reservas.calendar', [
            'pacientes' => $pacientes,
            'medicos' => $medicos,
            'tipos' => $tipos,
            'agenda' => $agenda,
            'selectedDate' => $selectedDate,
            'stats' => $stats,
        ]);
    }

    public function events(Request $request)
    {
        $query = Reserva::query()->with(['paciente', 'entrenador', 'tipocita']);

        if ($request->filled('entrenador_id')) {
            $query->where('entrenador_id', $request->integer('entrenador_id'));
        }

        $events = $query->get()->map(function (Reserva $reserva) {
            $start = $reserva->fecha;
            $end = $reserva->fin ?? ($start?->copy()->addMinutes($reserva->duracion ?? 60));
            $tipo = $reserva->tipocita?->nombre ?? $reserva->tipo ?? 'Cita';
            $paciente = $reserva->paciente?->nombres ?? 'Paciente sin nombre';
            $medico = $reserva->entrenador?->name;

            $title = $paciente;
            if ($tipo) {
                $title .= "\n{$tipo}";
            }
            if ($medico) {
                $title .= "\n{$medico}";
            }

            return [
                'id' => $reserva->id,
                'title' => $title,
                'start' => $start?->toIso8601String(),
                'end' => $end?->toIso8601String(),
                'backgroundColor' => $this->colorForEstado($reserva->estado),
                'borderColor' => $this->colorForEstado($reserva->estado),
                'duration' => $reserva->duracion,
                'paciente_id' => $reserva->paciente_id,
                'entrenador_id' => $reserva->entrenador_id,
                'tipocita_id' => $reserva->tipocita_id,
                'status' => $reserva->estado,
                'type' => $tipo,
                'modalidad' => $reserva->modalidad,
                'visita_tipo' => $reserva->visita_tipo,
                'motivo' => $reserva->nota_cliente,
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'entrenador_id' => ['required', 'integer', 'exists:users,id'],
            'tipocita_id' => ['nullable', 'integer', 'exists:tipocitas,id'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'duracion' => ['required', 'integer', 'min:15', 'max:240'],
            'estado' => ['nullable', 'string'],
            'modalidad' => ['required', 'string', 'in:Presencial,Online'],
            'visita_tipo' => ['required', 'string', 'in:Primera visita,Control'],
        ]);

        $validator->validate();

        $inicio = Carbon::createFromFormat('Y-m-d H:i', $request->input('fecha') . ' ' . $request->input('hora'));
        $tipo = Tipocita::find($request->input('tipocita_id'));

        $reserva = Reserva::create([
            'paciente_id' => $request->integer('paciente_id'),
            'entrenador_id' => $request->integer('entrenador_id'),
            'tipocita_id' => $request->input('tipocita_id'),
            'fecha' => $inicio,
            'duracion' => $request->integer('duracion'),
            'estado' => $request->input('estado', 'Pendiente'),
            'tipo' => $tipo?->nombre ?? 'Reserva',
            'nota_cliente' => $request->input('nota_cliente'),
            'modalidad' => $request->input('modalidad', 'Presencial'),
            'visita_tipo' => $request->input('visita_tipo', 'Control'),
        ]);

        return Redirect::route('reservas.calendar')->with('status', 'Cita creada correctamente.');
    }

    public function update(Request $request, Reserva $reserva)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => ['required', 'integer', 'exists:pacientes,id'],
            'entrenador_id' => ['required', 'integer', 'exists:users,id'],
            'tipocita_id' => ['nullable', 'integer', 'exists:tipocitas,id'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'duracion' => ['required', 'integer', 'min:15', 'max:240'],
            'estado' => ['nullable', 'string'],
            'modalidad' => ['required', 'string', 'in:Presencial,Online'],
            'visita_tipo' => ['required', 'string', 'in:Primera visita,Control'],
        ]);

        $validator->validate();

        $inicio = Carbon::createFromFormat('Y-m-d H:i', $request->input('fecha') . ' ' . $request->input('hora'));
        $tipo = Tipocita::find($request->input('tipocita_id'));

        $reserva->update([
            'paciente_id' => $request->integer('paciente_id'),
            'entrenador_id' => $request->integer('entrenador_id'),
            'tipocita_id' => $request->input('tipocita_id'),
            'fecha' => $inicio,
            'duracion' => $request->integer('duracion'),
            'estado' => $request->input('estado', $reserva->estado),
            'tipo' => $tipo?->nombre ?? $reserva->tipo,
            'nota_cliente' => $request->input('nota_cliente'),
            'modalidad' => $request->input('modalidad', $reserva->modalidad),
            'visita_tipo' => $request->input('visita_tipo', $reserva->visita_tipo),
        ]);

        return Redirect::route('reservas.calendar')->with('status', 'Cita actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return Redirect::route('reservas.calendar')->with('status', 'Cita eliminada.');
    }

    public function cancel(Reserva $reserva)
    {
        $reserva->update(['estado' => 'Cancelada']);

        return response()->json([
            'reserva' => $reserva,
        ]);
    }

    public function availability(Request $request)
    {
        $date = Carbon::parse($request->input('date', now()->toDateString()));
        $entrenadorId = $request->input('entrenador_id');

        $start = $date->copy()->setTime(8, 0);
        $end = $date->copy()->setTime(18, 0);

        $existing = Reserva::query()
            ->when($entrenadorId, fn($q) => $q->where('entrenador_id', $entrenadorId))
            ->whereDate('fecha', $date->toDateString())
            ->get();

        $slots = [];
        $cursor = $start->copy();

        while ($cursor < $end) {
            $slotEnd = $cursor->copy()->addMinutes(30);
            $overlaps = $existing->first(function (Reserva $reserva) use ($cursor, $slotEnd) {
                $reservaStart = $reserva->fecha;
                $reservaEnd = $reserva->fin ?? $reservaStart->copy()->addMinutes($reserva->duracion ?? 60);

                return $reservaStart < $slotEnd && $reservaEnd > $cursor;
            });

            if (! $overlaps) {
                $slots[] = $cursor->format('H:i');
            }

            $cursor = $slotEnd;
        }

        return response()->json([
            'slots' => $slots,
            'minTime' => $start->format('H:i:s'),
            'maxTime' => $end->format('H:i:s'),
        ]);
    }

    public function pending()
    {
        $pendings = Reserva::where('estado', 'Pendiente')->orderBy('fecha')->get();

        return view('reservas.pending', ['reservas' => $pendings]);
    }

    public function confirmPending(Reserva $reserva)
    {
        $reserva->update(['estado' => 'Confirmada']);

        return Redirect::route('reservas.pending')->with('status', 'Cita confirmada.');
    }

    public function horario()
    {
        return Redirect::route('reservas.calendar');
    }

    public function show(Reserva $reserva)
    {
        return Redirect::route('reservas.calendar')->with('reserva', $reserva);
    }

    public function create()
    {
        return Redirect::route('reservas.calendar');
    }

    public function edit(Reserva $reserva)
    {
        return Redirect::route('reservas.calendar')->with('reserva', $reserva);
    }

    public function cobrar(Reserva $reserva)
    {
        return Redirect::route('reservas.calendar')->with('status', 'Pago registrado para la cita.');
    }

    private function colorForEstado(?string $estado): string
    {
        return match ($estado) {
            'Confirmada' => '#28a745',
            'Cancelada' => '#dc3545',
            'No Asistida' => '#0d6efd',
            default => '#ffc107',
        };
    }
}
