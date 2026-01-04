<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureService
{
    public function storeBase64(string $base64, ?int $tenantId = null): string
    {
        if (!str_contains($base64, ',')) {
            throw new \InvalidArgumentException('Firma inválida');
        }

        [$meta, $data] = explode(',', $base64, 2);
        $binary = base64_decode(trim($data), true);

        if ($binary === false) {
            throw new \InvalidArgumentException('Firma inválida');
        }

        $filename = 'consents/signatures/' . ($tenantId ? $tenantId . '/' : '') . Str::uuid() . '.png';
        if (! Storage::disk('public')->put($filename, $binary)) {
            throw new \RuntimeException('No se pudo guardar la firma');
        }

        return $filename;
    }
}
