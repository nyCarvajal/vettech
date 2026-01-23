<?php

namespace App\Notifications\Channels;

use App\Services\WhatsApp\OneMsgClient;
use Illuminate\Notifications\Notification;

class OneMsgChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $payload = $notification->toOneMsgTemplate($notifiable);
        if (! $payload) {
            return;
        }

        $phone = $notifiable->routeNotificationFor('onemsg') ?? $notifiable->telefono;

        if (! $phone) {
            return;
        }

        app(OneMsgClient::class)->sendTemplate($phone, $payload);
    }
}
