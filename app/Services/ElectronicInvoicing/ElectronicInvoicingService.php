<?php

namespace App\Services\ElectronicInvoicing;

use App\Models\Invoice;
use RuntimeException;

class ElectronicInvoicingService
{
    public function send(Invoice $invoice): ElectronicResult
    {
        throw new RuntimeException('Electronic invoicing not implemented yet.');
    }
}
