<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\WhatsAppWebhookController;

Route::get('reserva/availability', [ReservaController::class, 'availability']);
// routes/api.php  (o web.php si lo prefieres)
Route::get('/clientesb', [ClientesController::class, 'search'])->name('clientes.search');


Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'receive']);


?>
