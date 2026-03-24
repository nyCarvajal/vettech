<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    // 1. Verificación de Meta (Solo ocurre una vez al configurar)
    public function verify(Request $request)
    {
        $token = env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'); // Crea esta clave en tu .env
        $mode = $request->query('hub_mode');
        $hubToken = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $hubToken) {
            if ($mode === 'subscribe' && $hubToken === $token) {
                return response($challenge, 200);
            }
        }
        return response('Forbidden', 403);
    }

    // 2. Recepción de mensajes (Aquí llegan las respuestas de los médicos)
    public function receive(Request $request)
    {
        $data = $request->all();

        // Log para ver la estructura (revisa storage/logs/laravel.log)
        Log::info('WhatsApp Webhook:', $data);

        // Ejemplo básico para extraer el mensaje
        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            $mensaje = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $from = $mensaje['from']; // Teléfono del médico
            $text = $mensaje['text']['body'] ?? 'Mensaje no es de texto';

            // Aquí podrías disparar un Evento en Laravel para procesar con IA
            Log::info("Mensaje de $from: $text");
        }

        return response('EVENT_RECEIVED', 200);
    }
}
?>