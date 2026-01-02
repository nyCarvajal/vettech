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
use App\Http\Controllers\GroomingBillingController;
use App\Http\Controllers\GroomingController;
use App\Http\Controllers\GroomingReportController;
use App\Http\Controllers\GroomingStatusController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ClinicalAttachmentController;
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
Route::get('patients/{patient}/carnet', [\App\Http\Controllers\PatientVaccineCardController::class, 'show'])->name('patients.carnet');
Route::get('pacientes/{patient}/carnet/pdf', [\App\Http\Controllers\PatientVaccineCardController::class, 'pdf'])->name('patients.carnet.pdf');
Route::get('patients/{patient}/immunizations/create', [\App\Http\Controllers\PatientImmunizationController::class, 'create'])->name('patients.immunizations.create');
Route::post('patients/{patient}/immunizations', [\App\Http\Controllers\PatientImmunizationController::class, 'store'])->name('patients.immunizations.store');
Route::get('patients/{patient}/immunizations/{immunization}/edit', [\App\Http\Controllers\PatientImmunizationController::class, 'edit'])->name('patients.immunizations.edit');
Route::put('patients/{patient}/immunizations/{immunization}', [\App\Http\Controllers\PatientImmunizationController::class, 'update'])->name('patients.immunizations.update');
Route::delete('patients/{patient}/immunizations/{immunization}', [\App\Http\Controllers\PatientImmunizationController::class, 'destroy'])->name('patients.immunizations.destroy');
Route::get('patients/{patient}/dewormings/{type}/create', [\App\Http\Controllers\PatientDewormingController::class, 'create'])->name('patients.dewormings.create');
Route::post('patients/{patient}/dewormings', [\App\Http\Controllers\PatientDewormingController::class, 'store'])->name('patients.dewormings.store');
Route::get('patients/{patient}/dewormings/{deworming}/edit', [\App\Http\Controllers\PatientDewormingController::class, 'edit'])->name('patients.dewormings.edit');
Route::put('patients/{patient}/dewormings/{deworming}', [\App\Http\Controllers\PatientDewormingController::class, 'update'])->name('patients.dewormings.update');
Route::delete('patients/{patient}/dewormings/{deworming}', [\App\Http\Controllers\PatientDewormingController::class, 'destroy'])->name('patients.dewormings.destroy');
Route::get('/breeds', BreedsController::class)->name('breeds.index');

Route::prefix('peluqueria')
    ->name('groomings.')
    ->group(function () {
        Route::get('/', [GroomingController::class, 'index'])->name('index');
        Route::get('/crear', [GroomingController::class, 'create'])->name('create');
        Route::post('/', [GroomingController::class, 'store'])->name('store');
        Route::get('/{grooming}', [GroomingController::class, 'show'])->name('show');
        Route::post('/{grooming}/iniciar', [GroomingStatusController::class, 'start'])->name('start');
        Route::post('/{grooming}/cancelar', [GroomingStatusController::class, 'cancel'])->name('cancel');
        Route::get('/{grooming}/informe', [GroomingReportController::class, 'create'])->name('report.create');
        Route::post('/{grooming}/informe', [GroomingReportController::class, 'store'])->name('report.store');
        Route::post('/{grooming}/cobrar', [GroomingBillingController::class, 'charge'])->name('charge');
    });
		 
		 
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
  Route::get('historias-clinicas/{historiaClinica}/pdf', [HistoriaClinicaController::class, 'pdf'])
        ->name('historias-clinicas.pdf');
  Route::get('historias-clinicas/{historiaClinica}/adjuntos', [ClinicalAttachmentController::class, 'index'])
        ->name('historias-clinicas.adjuntos.index');
  Route::post('historias-clinicas/{historiaClinica}/adjuntos', [ClinicalAttachmentController::class, 'store'])
        ->name('historias-clinicas.adjuntos.store');
  Route::get('historias-clinicas/{historiaClinica}/recetario', [HistoriaClinicaController::class, 'createRecetario'])
        ->name('historias-clinicas.recetarios.create');
  Route::post('historias-clinicas/{historiaClinica}/recetario', [HistoriaClinicaController::class, 'storeRecetario'])
        ->name('historias-clinicas.recetarios.store');
  Route::post('recetarios/{prescription}/facturar', [HistoriaClinicaController::class, 'facturarRecetario'])
        ->name('historias-clinicas.recetarios.facturar');
  Route::get('recetarios/{prescription}/imprimir', [HistoriaClinicaController::class, 'imprimirRecetario'])
        ->name('historias-clinicas.recetarios.print');
  Route::get('historias-clinicas/{historiaClinica}/remision', [HistoriaClinicaController::class, 'createRemision'])
        ->name('historias-clinicas.remisiones.create');
  Route::post('historias-clinicas/{historiaClinica}/remision', [HistoriaClinicaController::class, 'storeRemision'])
        ->name('historias-clinicas.remisiones.store');
  Route::get('remisiones/{examReferral}/imprimir', [HistoriaClinicaController::class, 'imprimirRemision'])
        ->name('historias-clinicas.remisiones.print');
  Route::resource('historias-clinicas', HistoriaClinicaController::class, [
      'parameters' => ['historias-clinicas' => 'historiaClinica'],
  ]);
  Route::delete('adjuntos/{attachment}', [ClinicalAttachmentController::class, 'destroy'])
        ->name('historias-clinicas.adjuntos.destroy');
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

        Route::prefix('hospital')->name('hospital.')->group(function () {
            Route::get('/', [HospitalController::class, 'index'])->name('index');
            Route::get('/admit', [HospitalController::class, 'create'])->name('admit');
            Route::post('/admit', [HospitalController::class, 'store'])->name('store');
            Route::get('/board', \App\Http\Controllers\HospitalBoardController::class)->name('board');
            Route::get('/{stay}', [HospitalController::class, 'show'])->name('show');
            Route::post('/{stay}/discharge', [HospitalController::class, 'discharge'])->name('discharge');
            Route::post('/{stay}/invoice', [HospitalController::class, 'generateInvoice'])->name('invoice');
            Route::post('/{stay}/orders', [HospitalController::class, 'addOrder'])->name('orders.store');
            Route::post('/orders/{order}/stop', [HospitalController::class, 'stopOrder'])->name('orders.stop');
            Route::post('/orders/{order}/administrations', [HospitalController::class, 'addAdministration'])->name('orders.administrations');
            Route::post('/{stay}/vitals', [HospitalController::class, 'addVitals'])->name('vitals.store');
            Route::post('/{stay}/progress', [HospitalController::class, 'addProgress'])->name('progress.store');
            Route::post('/{stay}/charges', [HospitalController::class, 'addCharge'])->name('charges.store');
        });

        Route::prefix('hospital/stays')->name('hospital.stays.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HospitalStaysController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\HospitalStaysController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\HospitalStaysController::class, 'store'])->name('store');
        });
        Route::post('hospital/stays/{stay}/discharge', [\App\Http\Controllers\HospitalStaysController::class, 'discharge'])->name('hospital.stays.discharge');
        Route::resource('hospital/tasks', \App\Http\Controllers\HospitalTasksController::class)->only(['create', 'store']);
        Route::post('hospital/handoff', [\App\Http\Controllers\HandoffController::class, 'store'])->name('hospital.handoff.store');

        Route::resource('sales', \App\Http\Controllers\SalesController::class)->only(['index', 'store', 'show']);
        Route::get('cash/sessions', [\App\Http\Controllers\CashSessionsController::class, 'index'])->name('cash.sessions.index');
        Route::post('cash/sessions', [\App\Http\Controllers\CashSessionsController::class, 'store'])->name('cash.sessions.store');
        Route::post('cash/sessions/{cashSession}/close', [\App\Http\Controllers\CashSessionsController::class, 'close'])->name('cash.sessions.close');
        Route::post('cash/movements', [\App\Http\Controllers\CashMovementsController::class, 'store'])->name('cash.movements.store');
    });
    Route::middleware([
        Authenticate::class,
        ConnectTenantDB::class,
    ])->group(function () {
        Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
        Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
        Route::get('{any}', [RoutingController::class, 'root'])->name('any');
    });


	
