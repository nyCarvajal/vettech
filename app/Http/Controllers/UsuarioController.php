<?php

namespace App\Http\Controllers;

use App\Models\TipoIdentificacion;
use App\Models\User;
use App\Support\RoleLabelResolver;
use Cloudinary\Cloudinary as CloudinarySdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($req, $next) {
            return $next($req);
        });
        $this->middleware('ensureRole:admin')
            ->only(['createTrainer', 'storeTrainer', 'createAdmin', 'storeAdmin']);
    }

    private const ROLE_OPTIONS = [
        'admin' => 'Administrador',
        'medico' => 'Médico',
        'contador' => 'Contador',
        'groomer' => 'Groomer',
    ];

    public function createTrainer()
    {
        $labels = $this->stylistLabels();

        return view('users.create_trainer', [
            'trainerLabelSingular' => $labels['singular'],
            'trainerLabelPlural' => $labels['plural'],
            'roles' => $this->roleOptions(),
            'defaultRole' => 'groomer',
            'tipoIdentificaciones' => $this->identificationTypes(),
        ]);
    }

    public function storeTrainer(Request $request)
    {
        $data = $this->validateUser($request);

        $data['email'] = strtolower($data['email']);

        $signatureData = $this->processSignatureUpload($request);

        $this->createUser($data, $signatureData);

        return redirect()
            ->route('users.index')
            ->with('success', $this->roleOptions()[$data['role']] . ' creado correctamente.');
    }

    public function storeAdmin(Request $request)
    {
        $data = $this->validateUser($request);

        $data['email'] = strtolower($data['email']);

        $signatureData = $this->processSignatureUpload($request);

        $this->createUser($data, $signatureData);

        return redirect()
            ->route('users.index')
            ->with('success', $this->roleOptions()[$data['role']] . ' creado correctamente.');
    }

    public function createAdmin()
    {
        return view('users.create_admin', [
            'roles' => $this->roleOptions(),
            'defaultRole' => 'admin',
            'tipoIdentificaciones' => $this->identificationTypes(),
        ]);
    }

    public function index()
    {
        $clinicaId = Auth::user()->clinica_id ?? Auth::user()->peluqueria_id;

        $users = User::with('peluqueria')
            ->when($clinicaId, fn ($query) => $query->where('clinica_id', $clinicaId))
            ->orderBy('nombres')
            ->paginate(15);

        $labels = $this->stylistLabels();

        return view('users.index', [
            'users' => $users,
            'trainerLabelSingular' => $labels['singular'],
            'trainerLabelPlural' => $labels['plural'],
            'roleLabels' => $this->roleOptions(),
        ]);
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => $this->roleOptions(),
            'tipoIdentificaciones' => $this->identificationTypes(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);

        $data['email'] = strtolower($data['email']);

        $signatureData = $this->processSignatureUpload($request, $user);

        $updateData = [
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'tipo_identificacion' => $data['tipo_identificacion'],
            'numero_identificacion' => $data['numero_identificacion'],
            'direccion' => $data['direccion'],
            'whatsapp' => $data['whatsapp'],
            'color' => $data['color'] ?? null,
            'role' => $data['role'],
            'firma_medica_texto' => $signatureData['firma_medica_texto'],
            'firma_medica_url' => $signatureData['firma_medica_url'],
            'firma_medica_public_id' => $signatureData['firma_medica_public_id'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    protected function stylistLabels(): array
    {
        return RoleLabelResolver::forStylist();
    }

    protected function roleOptions(): array
    {
        return self::ROLE_OPTIONS;
    }

    protected function identificationTypes()
    {
        return TipoIdentificacion::on('tenant')
            ->orderBy('tipo')
            ->get();
    }

    protected function validateUser(Request $request, ?User $user = null): array
    {
        $emailRule = Rule::unique('users', 'email');

        if ($user) {
            $emailRule = $emailRule->ignore($user->id);
        }

        $passwordRule = $user ? 'nullable|string|confirmed|min:8' : 'required|string|confirmed|min:8';

        return $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', $emailRule],
            'tipo_identificacion' => 'required|string|max:50',
            'numero_identificacion' => 'required|string|max:50',
            'direccion' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'password' => $passwordRule,
            'color' => 'nullable|string|max:7',
            'role' => ['required', 'string', Rule::in(array_keys($this->roleOptions()))],
            'firma_medica_texto' => 'nullable|string|max:500',
            'firma_medica_imagen' => 'nullable|image|max:4096',
        ]);
    }

    protected function createUser(array $data, array $signatureData = []): void
    {
        $nombres = trim($data['nombre'] . ' ' . $data['apellidos']);

        $signatureDefaults = [
            'firma_medica_texto' => null,
            'firma_medica_url' => null,
            'firma_medica_public_id' => null,
        ];

        $signatureData = array_merge($signatureDefaults, $signatureData);

        DB::connection('mysql')
            ->table('users')
            ->insert([
                'nombres' => $nombres,
                'email' => $data['email'],
                'clinica_id' => Auth::user()->clinica_id,
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
                'role' => $data['role'],
                'firma_medica_texto' => $signatureData['firma_medica_texto'],
                'firma_medica_url' => $signatureData['firma_medica_url'],
                'firma_medica_public_id' => $signatureData['firma_medica_public_id'],
                'remember_token' => Str::random(60),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    protected function processSignatureUpload(Request $request, ?User $user = null): array
    {
        $file = $request->file('firma_medica_imagen');

        $existingData = [
            'firma_medica_texto' => $request->string('firma_medica_texto')->trim()->value() ?? null,
            'firma_medica_url' => $user?->firma_medica_url,
            'firma_medica_public_id' => $user?->firma_medica_public_id,
        ];

        if (! $file) {
            return $existingData;
        }

        $this->ensureCloudinaryConfigured();
        $cloudinary = $this->cloudinary();

        $upload = $cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => sprintf('clinicas/%s/firmas', Auth::user()->clinica_id ?? 'general'),
            'resource_type' => 'image',
            'transformation' => [['quality' => 'auto', 'fetch_format' => 'auto', 'width' => 1600, 'crop' => 'limit']],
        ]);

        if ($user && $user->firma_medica_public_id) {
            $this->ensureCloudinaryConfigured();
            $cloudinary = $this->cloudinary();
            $cloudinary->uploadApi()->destroy($user->firma_medica_public_id, ['resource_type' => 'image']);
        }

        return [
            'firma_medica_texto' => $existingData['firma_medica_texto'],
            'firma_medica_url' => $upload['secure_url'] ?? ($upload['url'] ?? null),
            'firma_medica_public_id' => $upload['public_id'] ?? $existingData['firma_medica_public_id'],
        ];
    }

    private function ensureCloudinaryConfigured(): void
    {
        $cloudConfig = config('cloudinary.cloud');

        if (! is_array($cloudConfig)) {
            throw new \RuntimeException('Cloudinary configuration missing. Set CLOUDINARY_URL or CLOUDINARY_API_KEY/SECRET.');
        }

        if (empty($cloudConfig['cloud_name']) || empty($cloudConfig['api_key']) || empty($cloudConfig['api_secret'])) {
            throw new \RuntimeException('Cloudinary credentials missing. Set CLOUDINARY_URL or CLOUDINARY_API_KEY/SECRET.');
        }
    }

    private function cloudinary(): CloudinarySdk
    {
        return new CloudinarySdk(config('cloudinary'));
    }
}
