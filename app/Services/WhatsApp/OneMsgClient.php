<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
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

        $response = Http::asJson()->post($url, $payload);

        if (! $response->successful()) {
            $response->throw();
        }

        $json = $response->json();
        if (! $json || ! ($json['sent'] ?? false)) {
            throw new \RuntimeException(
                '1MSG error: ' . ($json['message'] ?? $json['error'] ?? 'unknown')
            );
        }
    }

    private function buildUrl(string $endpoint): string
    {
        $channelId = config('services.onemsg.channel_id');
        $token = config('services.onemsg.token');

        return sprintf('https://api.1msg.io/%s/%s?token=%s', $channelId, $endpoint, $token);
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
