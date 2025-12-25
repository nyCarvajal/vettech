<?php

use App\Http\Controllers\RoutingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\CanchaController;

use App\Http\Controllers\DeporteController;
use App\Http\Controllers\ClinicaController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\OrdendecompraController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\RecordatorioController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TipoUsuarioController;
use App\Http\Controllers\TipocitaController;
use App\Http\Controllers\TipoIdentificacionController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\OwnersController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\BreedsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdministrativeReportController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Middleware\ConnectTenantDB;
use App\Http\Controllers\ContadorDashboardController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\MedicoDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Public\BookingController;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Auth\Middleware\Authenticate;


require __DIR__ . '/auth.php';

Route::prefix('p/{clinica:slug}')
    ->name('public.booking.')
    ->group(function () {
        Route::get('/', function (\App\Models\Clinica $clinica) {
            return redirect()->route('public.booking.show', $clinica);
        })->name('index');
        Route::get('/agenda', [BookingController::class, 'show'])->name('show');
        Route::post('/registro', [BookingController::class, 'register'])->name('register');
        Route::post('/login', [BookingController::class, 'login'])->name('login');
        Route::post('/logout', [BookingController::class, 'logout'])->name('logout');
        Route::post('/citas', [BookingController::class, 'schedule'])->name('appointment');
        Route::get('/verificar', [BookingController::class, 'verify'])->name('verify');
        Route::get('/disponibilidad', [BookingController::class, 'availability'])->name('availability');
    });

// Rutas públicas
Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('/auth/signin', [LoginController::class, 'showLoginForm'])
     ->name('auth.showLoginForm');

// Rutas protegidas
//Route::middleware('auth')->group(function () {
  //  Route::post('logout', [LoginController::class, 'logout'])->name('logout');
// });


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
     ->middleware('auth')
     ->name('logout');


Route::middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        Authenticate::class,           // 1️⃣ primero autentica
        ConnectTenantDB::class, // 2️⃣ luego conecta tenant
        SubstituteBindings::class,

    ])
         ->group(function () {

Route::resource('owners', OwnersController::class);
Route::resource('patients', PatientsController::class);
Route::get('/breeds', BreedsController::class)->name('breeds.index');
		 
		 
Route::get('/ordenes/{orden}/pdf', [OrdendecompraController::class, 'pdf'])->name('ordenes.pdf');
Route::post('/ordenes/{orden}/email', [OrdendecompraController::class, 'sendEmail'])->name('ordenes.email');


Route::resource('proveedores', ProveedorController::class);
Route::resource('tipo-identificaciones', TipoIdentificacionController::class)->except(['show']);
Route::resource('areas', AreaController::class)->except(['show', 'destroy']);
		 
        Route::get('/dashboard', DashboardRedirectController::class)
            ->name('dashboard');

        Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])
            ->middleware('ensureRole:admin')
            ->name('dashboard.admin');

        Route::get('/dashboard/medico', [MedicoDashboardController::class, 'index'])
            ->middleware('ensureRole:medico')
            ->name('dashboard.medico');

        Route::get('/dashboard/contador', [ContadorDashboardController::class, 'index'])
            ->middleware('ensureRole:contador')
            ->name('dashboard.contador');

    Route::get('users/entrenadores/create', 
              [UsuarioController::class, 'createTrainer'])
         ->name('users.trainers.create');

Route::post('users/entrenadores', 
               [UsuarioController::class, 'storeTrainer'])
         ->name('users.trainers.store');

Route::get('users/administradores/create', 
              [UsuarioController::class, 'createAdmin'])
         ->name('users.admins.create');
Route::post('users/administradores', 
               [UsuarioController::class, 'storeAdmin'])
         ->name('users.admins.store');
Route::get('users', [UsuarioController::class,'index'])
     ->name('users.index');
Route::get('users/{user}/edit', [UsuarioController::class,'edit'])
     ->name('users.edit');
Route::put('users/{user}', [UsuarioController::class,'update'])
     ->name('users.update');
Route::delete('users/{user}', [UsuarioController::class,'destroy'])
     ->name('users.destroy');
Route::get('reservas/horario', [ReservaController::class, 'horario'])->name('reservas.horario');
Route::get('reservas/pendientes', [ReservaController::class, 'pending'])->name('reservas.pending');
Route::post('reservas/{reserva}/confirmar-publico', [ReservaController::class, 'confirmPending'])->name('reservas.pending.confirm');
Route::resource('proveedores', ProveedorController::class);
Route::get('/clientesb', [ClientesController::class, 'search'])->name('clientes.search');
Route::resource('tipo-usuarios', TipoUsuarioController::class);
Route::resource('tipocitas', TipocitaController::class)->except(['show']);
Route::resource('clinicas', ClinicaController::class);
Route::get('clinica/editar',     [ClinicaController::class, 'editOwn'])
         ->name('clinicas.edit-own');
Route::put('clinica/editar',     [ClinicaController::class, 'updateOwn'])
         ->name('clinicas.update-own');
Route::get('clinica/perfil', [ClinicaController::class,'showOwn'])
     ->name('clinicas.perfil');
  Route::get('/', function () {
      return redirect()->route('items.index');
  });
  Route::resource('items', ItemController::class);
  Route::post('historias-clinicas/autoguardado', [HistoriaClinicaController::class, 'autoSave'])
       ->name('historias-clinicas.autosave');
  Route::resource('historias-clinicas', HistoriaClinicaController::class, [
      'parameters' => ['historias-clinicas' => 'historiaClinica'],
  ]);
  Route::get('items/{item}/agregar-unidades', [ItemController::class, 'addUnitsForm'])->name('items.add-units-form');
  Route::post('items/{item}/agregar-unidades', [ItemController::class, 'addUnits'])->name('items.add-units');
Route::get('/calendar', [ReservaController::class, 'calendar'])->name('reservas.calendar');
Route::get('/reservas.json', [ReservaController::class, 'events'])
     ->name('reservas.events');
Route::post('reservas/{reserva}/cobrar', [ReservaController::class, 'cobrar'])
     ->name('reservas.cobrar');
Route::post('reservas/{reserva}/cancelar', [ReservaController::class, 'cancel'])
     ->name('reservas.cancel');
Route::resource('reservas', ReservaController::class);
Route::resource('clases',  ClaseController::class);
//	Route::resource('torneos', TorneoController::class);
Route::get('reservas.json', [ReservaController::class, 'events'])->name('reservas.events');
Route::resource('niveles', NivelController::class)->except(['create', 'edit', 'show']);
Route::resource('membresias', MembresiaController::class)->except(['create', 'edit', 'show']);
Route::resource('canchas', CanchaController::class);
    Route::resource('deportes', DeporteController::class);
    Route::resource('nivels', NivelController::class);
	Route::resource('usuario', UsuarioController::class);
	Route::resource('clase', ClaseController::class);
        Route::get('clientes/cumpleanos', [ClientesController::class, 'birthdays'])
            ->name('clientes.birthdays');
        Route::get('clientes/reengage', [ClientesController::class, 'reengage'])
            ->name('clientes.reengage');
        Route::resource('clientes', ClientesController::class);
        Route::resource('pacientes', ClientesController::class);
        Route::resource('clinica', ClinicaController::class);
        Route::resource('membresias', MembresiaController::class);
        Route::resource('recordatorio', RecordatorioController::class);
        Route::resource('reservas', ReservaController::class);
         // routes/web.php
Route::middleware(['auth'])
      ->resource('membresia-cliente', \App\Http\Controllers\MembresiaClienteController::class)
      ->only(['edit','update']);    // solo las que necesitamos

	 
	 });
	 
	 
	 
Route::get('reserva/availability', [ReservaController::class, 'availability']);
Route::get('/', [ClientesController::class, 'create']);
Route::get('/departamentos', [LocationController::class, 'departamentos']);
	Route::get('/municipios',    [LocationController::class, 'municipios']);
	
Route::match(['GET','POST'], 'webhook/whatsapp',
    \App\Http\Controllers\WhatsappWebhookController::class);




 Route::get('', [RoutingController::class, 'index'])->name('root');

// Vettech módulo V1
Route::middleware(['auth'])
    ->prefix('vet')
    ->group(function () {
        Route::resource('products', \App\Http\Controllers\ProductsController::class)->except(['show', 'destroy']);
        Route::resource('batches', \App\Http\Controllers\BatchesController::class)->only(['index', 'create', 'store']);
        Route::get('kardex', [\App\Http\Controllers\KardexController::class, 'index'])->name('kardex.index');

        Route::resource('prescriptions', \App\Http\Controllers\PrescriptionsController::class)->only(['index', 'create', 'store']);
        Route::get('dispensations', [\App\Http\Controllers\DispensationsController::class, 'index'])->name('dispensations.index');
        Route::post('dispensations/{prescription}', [\App\Http\Controllers\DispensationsController::class, 'store'])->name('dispensations.store');

        Route::get('hospital/board', \App\Http\Controllers\HospitalBoardController::class)->name('hospital.board');
        Route::resource('hospital/stays', \App\Http\Controllers\HospitalStaysController::class)->only(['index', 'create', 'store']);
        Route::post('hospital/stays/{stay}/discharge', [\App\Http\Controllers\HospitalStaysController::class, 'discharge'])->name('hospital.stays.discharge');
        Route::resource('hospital/tasks', \App\Http\Controllers\HospitalTasksController::class)->only(['create', 'store']);
        Route::post('hospital/handoff', [\App\Http\Controllers\HandoffController::class, 'store'])->name('hospital.handoff.store');

        Route::resource('sales', \App\Http\Controllers\SalesController::class)->only(['index', 'store', 'show']);
        Route::get('cash/sessions', [\App\Http\Controllers\CashSessionsController::class, 'index'])->name('cash.sessions.index');
        Route::post('cash/sessions', [\App\Http\Controllers\CashSessionsController::class, 'store'])->name('cash.sessions.store');
        Route::post('cash/sessions/{cashSession}/close', [\App\Http\Controllers\CashSessionsController::class, 'close'])->name('cash.sessions.close');
        Route::post('cash/movements', [\App\Http\Controllers\CashMovementsController::class, 'store'])->name('cash.movements.store');
    });
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');


	
