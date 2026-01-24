<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OneMsgClient
{
    public function sendMessage(string $phone, string $message): void
    {
        $payload = [
            'phone' => $this->formatPhone($phone),
            'body' => $message,
        ];

        $this->post('sendMessage', $payload);
    }

    public function sendFile(string $phone, string $fileUrl, string $filename, ?string $caption = null): void
    {
        $payload = [
            'phone' => $this->formatPhone($phone),
            'body' => $fileUrl,
            'filename' => $filename,
        ];

        if ($caption) {
            $payload['caption'] = $caption;
        }

        $this->post('sendFile', $payload);
    }

    public function sendTemplate(string $phone, array $payload): void
    {
        $payload['phone'] = $this->formatPhone($phone);

        $this->post('sendTemplate', $payload);
    }

    private function post(string $endpoint, array $payload): void
    {
        $url = $this->buildUrl($endpoint);
        $safeUrl = $this->buildUrl($endpoint, true);

        Log::channel('onemsg')->info('OneMsg request', [
            'url' => $safeUrl,
            'payload' => $payload,
        ]);

        $response = Http::asJson()->post($url, $payload);

        if (! $response->successful()) {
            Log::channel('onemsg')->warning('OneMsg response error', [
                'url' => $safeUrl,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $response->throw();
        }

        $json = $response->json();
        if (! $json || ! ($json['sent'] ?? false)) {
            Log::channel('onemsg')->warning('OneMsg response not sent', [
                'url' => $safeUrl,
                'response' => $json,
            ]);
            throw new \RuntimeException(
                '1MSG error: ' . ($json['message'] ?? $json['error'] ?? 'unknown')
            );
        }
    }

    private function buildUrl(string $endpoint, bool $redactToken = false): string
    {
        $channelId = config('services.onemsg.channel_id');
        $token = config('services.onemsg.token');
        $tokenParam = $redactToken ? '***' : $token;

        return sprintf('https://api.1msg.io/%s/%s?token=%s', $channelId, $endpoint, $tokenParam);
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone ?? '');
        $digits = ltrim($digits ?? '', '0');

        $countryCode = (string) config('services.onemsg.country_code', '57');

        if ($digits !== '' && ! Str::startsWith($digits, $countryCode)) {
            $digits = $countryCode . $digits;
        }

        return $digits;
    }
}
