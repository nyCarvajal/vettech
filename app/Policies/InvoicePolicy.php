<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return Gate::allows('sales.discount') || Gate::allows('sales.void');
    }

    public function void(User $user, Invoice $invoice): bool
    {
        return Gate::allows('sales.void') || $user->hasRole('admin');
    }
}
