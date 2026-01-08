<?php

namespace App\Services\ElectronicInvoicing;

class ElectronicResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $payload = []
    ) {
    }
}
