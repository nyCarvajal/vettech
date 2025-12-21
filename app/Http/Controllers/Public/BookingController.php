<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ClienteVerificationMail;
use App\Mail\NuevaReservaClinicaMail;
use App\Models\Cliente;
use App\Models\Clinica;
use App\Models\Reserva;
use App\Models\Tipocita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Support\RoleLabelResolver;

class BookingController extends Controller
{
    private const DEFAULT_DURATION = 60;

    public function show(Request $request, Clinica $clinica)
    {
        $this->setTenantConnection($clinica);

        $cliente = $this->currentClient($clinica);
        $tipocitas = Tipocita::orderBy('nombre')->get();
        $estilistas = $this->availableStylists($clinica);
        $proximasReservas = collect();

        $stylistLabels = $this->stylistLabels($clinica);

        if ($cliente) {
            $proximasReservas = Reserva::where('cliente_id', $cliente->id)
                ->orderBy('fecha')
                ->whereDate('fecha', '>=', Carbon::today())
                ->take(5)
                ->get();
        }

        return view('public.booking', [
            'clinica' => $clinica,
            'tipocitas' => $tipocitas,
            'cliente' => $cliente,
            'proximasReservas' => $proximasReservas,
            'defaultDuration' => self::DEFAULT_DURATION,
            'clinicaLogo' => $this->resolveLogoUrl($clinica),
            'estilistas' => $estilistas,
            'trainerLabelSingular' => $stylistLabels['singular'],
            'trainerLabelPlural' => $stylistLabels['plural'],
        ]);
    }

    public function register(Request $request, Clinica $clinica)
    {
        $this->setTenantConnection($clinica);

        $validator = Validator::make($request->all(), [
            'nombres' => ['required', 'string', 'max:200'],
            'apellidos' => ['nullable', 'string', 'max:200'],
            'correo' => ['required', 'email', 'max:200'],
            'whatsapp' => ['nullable', 'string', 'max:200'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha' => ['required', 'integer'],
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'captcha.required' => 'Resuelve el captcha para continuar.',
            'captcha.integer' => 'El captcha debe ser un número.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'register')->withInput();
        }

        $data = $validator->validated();
        $correo = strtolower($data['correo']);

        $captchaData = $request->session()->get($this->captchaKey($clinica));
        $captchaAnswer = $captchaData['answer'] ?? null;
        if (! $captchaAnswer || (int) $data['captcha'] !== (int) $captchaAnswer) {
            return back()
                ->withErrors(['captcha' => 'La respuesta del captcha es incorrecta.'], 'register')
                ->withInput($request->except('captcha'));
        }

        if (Cliente::where('correo', $correo)->exists()) {
            return back()
                ->withErrors(['correo' => 'Este correo ya está registrado.'], 'register')
                ->withInput();
        }

        $cliente = new Cliente([
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'] ?? null,
            'correo' => $correo,
            'whatsapp' => $data['whatsapp'] ?? null,
        ]);

        $cliente->password = Hash::make($data['password']);
        $cliente->verification_token = Str::random(64);
        $cliente->save();

        $verifyUrl = $this->verificationUrl($clinica, $cliente);
        Mail::to($cliente->correo)->send(new ClienteVerificationMail($clinica, $cliente, $verifyUrl));

        $request->session()->forget($this->sessionKey($clinica));
        $request->session()->put($this->pendingKey($clinica), $cliente->correo);
        $request->session()->forget($this->captchaKey($clinica));

        return redirect()
            ->route('public.booking.show', $clinica)
            ->with('status', 'Registro exitoso. Revisa tu correo para verificar tu cuenta.');
    }

    public function login(Request $request, Clinica $clinica)
    {
        $this->setTenantConnection($clinica);

        $validator = Validator::make($request->all(), [
            'correo' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'login')->withInput();
        }

        $data = $validator->validated();
        $correo = strtolower($data['correo']);

        $cliente = Cliente::where('correo', $correo)->first();

        if (! $cliente || empty($cliente->password) || ! Hash::check($data['password'], $cliente->password)) {
            return back()
                ->withErrors(['correo' => 'Credenciales inválidas.'], 'login')
                ->withInput($request->only('correo'));
        }

        if (! $cliente->email_verified_at) {
            return back()
                ->withErrors(['correo' => 'Debes verificar tu correo antes de agendar.'], 'login')
                ->withInput($request->only('correo'));
        }

        $request->session()->put($this->sessionKey($clinica), $cliente->id);
        $request->session()->forget($this->pendingKey($clinica));
        $request->session()->regenerate();

        return redirect()
            ->route('public.booking.show', $clinica)
            ->with('status', 'Bienvenido de nuevo. Ya puedes agendar tu cita.');
    }

    public function logout(Request $request, Clinica $clinica)
    {
        $request->session()->forget($this->sessionKey($clinica));
        $request->session()->forget($this->pendingKey($clinica));
        $request->session()->regenerateToken();

        return redirect()->route('public.booking.show', $clinica)
            ->with('status', 'Has cerrado sesión correctamente.');
    }

    public function schedule(Request $request, Clinica $clinica)
    {
        $this->setTenantConnection($clinica);

        if ($request->has('entrenador_id')) {
            $request->merge([
                'entrenador_id' => $this->normalizeStylistId($request->input('entrenador_id')),
            ]);
        }

        $stylistLabels = $this->stylistLabels($clinica);

        $validator = Validator::make($request->all(), [
            'nombres' => ['required', 'string', 'max:200'],
            'apellidos' => ['nullable', 'string', 'max:200'],
            'whatsapp' => ['required', 'string', 'max:200'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'tipocita_id' => ['nullable', 'integer', 'exists:tipocita,id'],
            'nota_cliente' => ['nullable', 'string', 'max:1000'],
            'entrenador_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($clinica, $stylistLabels) {
                    if (! $this->stylistExists($clinica, (int) $value)) {
                        $fail('El ' . $stylistLabels['singular_lower'] . ' seleccionado no es válido.');
                    }
                },
            ],
        ], [
            'nombres.required' => 'Ingresa tu nombre.',
            'nombres.max' => 'El nombre no puede superar 200 caracteres.',
            'apellidos.max' => 'Los apellidos no pueden superar 200 caracteres.',
            'whatsapp.required' => 'Indica tu número de WhatsApp.',
            'whatsapp.max' => 'El número de WhatsApp no puede superar 200 caracteres.',
            'fecha.required' => 'Selecciona la fecha de tu cita.',
            'hora.required' => 'Selecciona la hora de tu cita.',
            'tipocita_id.exists' => 'El tipo de cita seleccionado no es válido.',
            'entrenador_id.required' => 'Selecciona a tu ' . $stylistLabels['singular_lower'] . ' que atenderá tu cita.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('public.booking.show', $clinica)
                ->withErrors($validator, 'appointment')
                ->withInput();
        }

        $data = $validator->validated();

        $cliente = Cliente::where('whatsapp', $data['whatsapp'])->first();

        if (! $cliente) {
            $cliente = new Cliente();
        }

        $cliente->fill([
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'] ?? null,
            'whatsapp' => $data['whatsapp'],
        ]);

        $cliente->save();

        $inicio = Carbon::createFromFormat('Y-m-d H:i', $data['fecha'] . ' ' . $data['hora']);
        if ($inicio->isPast()) {
            return redirect()
                ->route('public.booking.show', $clinica)
                ->withErrors(['fecha' => 'No puedes agendar en una fecha pasada.'], 'appointment')
                ->withInput();
        }

        $duracion = self::DEFAULT_DURATION;
        $fin = (clone $inicio)->addMinutes($duracion);

        $conflicto = Reserva::where('estado', '<>', 'Cancelada')
            ->when(! empty($data['entrenador_id']), function ($query) use ($data) {
                $query->where(function ($sub) use ($data) {
                    $sub->whereNull('entrenador_id')
                        ->orWhere('entrenador_id', $data['entrenador_id']);
                });
            })
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha', [$inicio, $fin->copy()->subSecond()])
                    ->orWhere(function ($sub) use ($inicio, $fin) {
                        $sub->where('fecha', '<=', $inicio)
                            ->whereRaw('DATE_ADD(fecha, INTERVAL duracion MINUTE) > ?', [$inicio->format('Y-m-d H:i:s')]);
                    });
            })
            ->exists();

        if ($conflicto) {
            return redirect()
                ->route('public.booking.show', $clinica)
                ->withErrors(['fecha' => 'El horario seleccionado ya no está disponible.'], 'appointment')
                ->withInput();
        }

        $tipoCita = null;
        if (! empty($data['tipocita_id'])) {
            $tipoCita = Tipocita::find($data['tipocita_id']);
        }

        $reserva = Reserva::create([
            'fecha' => $inicio,
            'duracion' => $duracion,
            'cliente_id' => $cliente->id,
            'estado' => 'Pendiente',
            'tipo' => $tipoCita?->nombre ?? 'Reserva',
            'nota_cliente' => $data['nota_cliente'] ?? null,
            'entrenador_id' => $data['entrenador_id'],
        ]);

        $recipients = $this->resolveSalonNotificationEmails($clinica);

        if (! empty($recipients)) {
            try {
                Mail::to($recipients)->send(new NuevaReservaClinicaMail($clinica, $cliente, $reserva));

                $failures = $this->resolveMailFailures();
                if (! empty($failures)) {
                    Log::warning('Fallo al enviar correo de nueva reserva a la peluquería.', [
                        'clinica_id' => $clinica->id,
                        'reserva_id' => $reserva->id,
                        'destinatarios_fallidos' => $failures,
                        'mail_debug' => $this->mailDebugContext(),
                    ]);
                } else {
                    Log::info('Correo de nueva reserva enviado a la peluquería.', [
                        'clinica_id' => $clinica->id,
                        'reserva_id' => $reserva->id,
                        'destinatarios' => $recipients,
                        'mail_debug' => $this->mailDebugContext(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::error('Error al enviar correo de nueva reserva a la peluquería.', [
                    'clinica_id' => $clinica->id,
                    'reserva_id' => $reserva->id,
                    'destinatarios' => $recipients,
                    'mensaje' => $exception->getMessage(),
                    'mail_debug' => $this->mailDebugContext(),
                ]);
            }
        } else {
            Log::info('Reserva creada sin correo de notificación para la peluquería: no hay destinatarios configurados.', [
                'clinica_id' => $clinica->id,
                'reserva_id' => $reserva->id,
            ]);
        }

        return redirect()
            ->route('public.booking.show', $clinica)
            ->with('status', 'Tu solicitud fue enviada. Te confirmaremos por WhatsApp.');
    }

    /**
     * Intenta recuperar los destinatarios que el mailer reporta como fallidos.
     *
     * Algunos drivers no implementan el método "failures", por lo que en ese
     * caso devolvemos un arreglo vacío para evitar excepciones al consultar.
     */
    private function resolveMailFailures(): array
    {
        $mailer = Mail::getFacadeRoot();

        if ($mailer && method_exists($mailer, 'failures')) {
            return $mailer->failures();
        }

        return [];
    }

    private function mailDebugContext(): array
    {
        $defaultMailer = config('mail.default');
        $mailers = config('mail.mailers', []);
        $selectedMailer = $mailers[$defaultMailer] ?? null;

        return [
            'app_env' => config('app.env'),
            'environment_file' => app()->environmentFile(),
            'environment_path' => app()->environmentPath(),
            'mail_default' => $defaultMailer,
            'mailer_configuration' => $selectedMailer,
            'from' => config('mail.from'),
            'mail_host' => data_get($selectedMailer, 'host'),
            'mail_username' => data_get($selectedMailer, 'username'),
            'mail_from_address' => data_get(config('mail.from'), 'address'),
            'env_file_exists' => file_exists(base_path('.env')),
            'dev_file_exists' => file_exists(base_path('.dev')),
        ];
    }

    /**
     * Obtiene los correos configurados para notificar a la peluquería.
     */
    private function resolveSalonNotificationEmails(Clinica $clinica): array
    {
        $candidateKeys = [
            'correo',
            'email',
            'correo_contacto',
            'email_contacto',
            'correo_reservas',
            'email_reservas',
        ];

        return collect($candidateKeys)
            ->map(fn (string $key) => $clinica->{$key} ?? null)
            ->filter(fn ($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    public function verify(Request $request, Clinica $clinica)
    {
        $this->setTenantConnection($clinica);

        $token = $request->query('token');
        $correo = strtolower((string) $request->query('email'));

        if (! $token || ! $correo) {
            return redirect()
                ->route('public.booking.show', $clinica)
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        $cliente = Cliente::where('correo', $correo)->first();

        if (! $cliente) {
            return redirect()
                ->route('public.booking.show', $clinica)
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        if ($cliente->email_verified_at) {
            $request->session()->put($this->sessionKey($clinica), $cliente->id);
            $request->session()->forget($this->pendingKey($clinica));
            $request->session()->regenerate();

            return redirect()
                ->route('public.booking.show', $clinica)
                ->with('status', 'Tu correo ya estaba verificado. Ya puedes agendar tu cita.');
        }

        if (! $cliente->verification_token || ! hash_equals((string) $cliente->verification_token, (string) $token)) {
            return redirect()
                ->route('public.booking.show', $clinica)
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        $cliente->markEmailAsVerified();

        $request->session()->put($this->sessionKey($clinica), $cliente->id);
        $request->session()->forget($this->pendingKey($clinica));
        $request->session()->regenerate();

        return redirect()
            ->route('public.booking.show', $clinica)
            ->with('status', 'Correo verificado correctamente. Ya puedes agendar tu cita.');
    }

    public function availability(Request $request, Clinica $clinica)
    {
        $this->setTenantConnection($clinica);

        $date = $request->query('date');
        $rawStylistId = $request->query('entrenador_id');
        $stylistId = $this->normalizeStylistId($rawStylistId);
        $stylistLabels = $this->stylistLabels($clinica);
        if (! $date) {
            return response()->json(['error' => 'Debes indicar la fecha.'], 422);
        }

        if ($rawStylistId !== null && $rawStylistId !== '' && $stylistId === null) {
            return response()->json(['error' => 'El ' . $stylistLabels['singular_lower'] . ' seleccionado no es válido.'], 422);
        }

        if ($stylistId && ! $this->stylistExists($clinica, $stylistId)) {
            return response()->json(['error' => 'El ' . $stylistLabels['singular_lower'] . ' seleccionado no es válido.'], 422);
        }

        try {
            $inicioJornada = Carbon::parse($date . ' 08:00:00');
            $finJornada = Carbon::parse($date . ' 20:00:00');
            $intervalo = 15;

            $reservas = Reserva::whereDate('fecha', $date)
                ->where('estado', '<>', 'Cancelada')
                ->when($stylistId, function ($query) use ($stylistId) {
                    $query->where(function ($sub) use ($stylistId) {
                        $sub->whereNull('entrenador_id')
                            ->orWhere('entrenador_id', $stylistId);
                    });
                })
                ->get(['fecha', 'duracion', 'entrenador_id']);

            $ocupados = [];
            foreach ($reservas as $reserva) {
                $inicio = Carbon::parse($reserva->fecha);
                $fin = (clone $inicio)->addMinutes((int) ($reserva->duracion ?? 0));
                $ocupados[] = [$inicio, $fin];
            }

            $slots = [];
            for ($cursor = $inicioJornada->copy(); $cursor->lt($finJornada); $cursor->addMinutes($intervalo)) {
                $finSlot = $cursor->copy()->addMinutes($intervalo);
                $choca = false;
                foreach ($ocupados as [$ocInicio, $ocFin]) {
                    if ($cursor < $ocFin && $finSlot > $ocInicio) {
                        $choca = true;
                        break;
                    }
                }

                if (! $choca) {
                    $slots[] = $cursor->format('H:i');
                }
            }

            return response()->json([
                'slots' => $slots,
                'inicio' => $inicioJornada->format('H:i'),
                'fin' => $finJornada->format('H:i'),
            ]);
        } catch (\Throwable $exception) {
            return response()->json(['error' => 'No se pudo calcular la disponibilidad.'], 500);
        }
    }

    private function availableStylists(Clinica $clinica)
    {
        $stylists = collect();

        foreach ($this->connectionsFor($clinica) as $connection) {
            $connectionStylists = User::on($connection)
                ->where('clinica_id', $clinica->id)
                ->whereIn('role', [11, '11'])
                ->orderBy('nombre')
                ->orderBy('apellidos')
                ->get();

            if ($connectionStylists->isEmpty()) {
                $connectionStylists = User::on($connection)
                    ->where('clinica_id', $clinica->id)
                    ->orderBy('nombre')
                    ->orderBy('apellidos')
                    ->get();
            }

            if ($connectionStylists->isNotEmpty()) {
                $stylists = $stylists->merge($connectionStylists);
            }
        }

        return $stylists->unique('id')->values();
    }

    private function normalizeStylistId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            $value = reset($value);
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return null;
            }

            if (str_contains($value, ':')) {
                $parts = array_values(array_filter(array_map('trim', explode(':', $value)), fn ($segment) => $segment !== ''));
                $value = end($parts) ?: reset($parts);
            }

            if (! is_numeric($value)) {
                preg_match_all('/\d+/', $value, $matches);
                if (! empty($matches[0])) {
                    $value = end($matches[0]);
                }
            }
        }

        if (! is_numeric($value)) {
            return null;
        }

        $intValue = (int) $value;

        return $intValue > 0 ? $intValue : null;
    }

    private function connectionsFor(Clinica $clinica): array
    {
        $connections = ['mysql'];

        if (! empty($clinica->db)) {
            $connections[] = 'tenant';
        }

        return array_unique($connections);
    }

    private function stylistExists(Clinica $clinica, int $stylistId): bool
    {
        foreach ($this->connectionsFor($clinica) as $connection) {
            $exists = User::on($connection)
                ->where('clinica_id', $clinica->id)
                ->where('id', $stylistId)
                ->exists();

            if ($exists) {
                return true;
            }
        }

        return false;
    }

    private function setTenantConnection(Clinica $clinica): void
    {
        config(['database.connections.tenant.database' => $clinica->db]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
    }

    private function currentClient(Clinica $clinica): ?Cliente
    {
        $id = session($this->sessionKey($clinica));
        if (! $id) {
            return null;
        }

        return Cliente::find($id);
    }

    private function verificationUrl(Clinica $clinica, Cliente $cliente): string
    {
        return route('public.booking.verify', [
            'clinica' => $clinica,
            'token' => $cliente->verification_token,
            'email' => $cliente->correo,
        ]);
    }

    private function sessionKey(Clinica $clinica): string
    {
        return 'public_cliente_' . $clinica->id;
    }

    private function pendingKey(Clinica $clinica): string
    {
        return 'public_cliente_pending_' . $clinica->id;
    }

    private function regenerateCaptcha(Request $request, Clinica $clinica): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        $captcha = [
            'question' => $a . ' + ' . $b,
            'answer' => $a + $b,
        ];

        $request->session()->put($this->captchaKey($clinica), $captcha);

        return $captcha;
    }

    private function captchaKey(Clinica $clinica): string
    {
        return 'public_captcha_' . $clinica->id;
    }

    private function stylistLabels(Clinica $clinica): array
    {
        $labels = RoleLabelResolver::forStylist($clinica);

        return [
            'singular' => $labels['singular'],
            'plural' => $labels['plural'],
            'singular_lower' => Str::lower($labels['singular']),
            'plural_lower' => Str::lower($labels['plural']),
        ];
    }

    private function resolveLogoUrl(Clinica $clinica): string
    {
        return $clinica->resolvedLogoUrl();
    }
}
