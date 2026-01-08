<?php

namespace App\Contracts;

use App\Models\Invoice;
use App\Services\ElectronicInvoicing\ElectronicResult;

interface ElectronicInvoicingProviderInterface
{
    public function sendInvoice(Invoice $invoice): ElectronicResult;
}
