<?php

namespace App\Http\Controllers;

use App\Models\TipoIdentificacion;
use App\Models\User;
use App\Support\RoleLabelResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $this->createUser($data);

        return redirect()
            ->route('users.index')
            ->with('success', $this->roleOptions()[$data['role']] . ' creado correctamente.');
    }

    public function storeAdmin(Request $request)
    {
        $data = $this->validateUser($request);

        $this->createUser($data);

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
        $peluqueriaId = Auth::user()->peluqueria_id;

        $users = User::with('peluqueria')
            ->where('peluqueria_id', $peluqueriaId)
            ->orderBy('nombre')
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
        $emailRule = 'required|email|unique:usuarios,email';

        if ($user) {
            $emailRule .= ',' . $user->id;
        }

        $passwordRule = $user ? 'nullable|string|confirmed|min:8' : 'required|string|confirmed|min:8';

        return $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => $emailRule,
            'tipo_identificacion' => 'required|string|max:50',
            'numero_identificacion' => 'required|string|max:50',
            'direccion' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'password' => $passwordRule,
            'color' => 'nullable|string|max:7',
            'role' => ['required', 'string', Rule::in(array_keys($this->roleOptions()))],
        ]);
    }

    protected function createUser(array $data): void
    {
        User::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'tipo_identificacion' => $data['tipo_identificacion'],
            'numero_identificacion' => $data['numero_identificacion'],
            'direccion' => $data['direccion'],
            'whatsapp' => $data['whatsapp'],
            'password' => Hash::make($data['password']),
            'peluqueria_id' => Auth::user()->peluqueria_id,
            'role' => $data['role'],
            'color' => $data['color'] ?? null,
        ]);
    }
}
