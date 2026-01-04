<?php

namespace App\Policies;

use App\Models\Procedure;
use App\Models\User;

class ProcedurePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Procedure $procedure): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Procedure $procedure): bool
    {
        return true;
    }

    public function delete(User $user, Procedure $procedure): bool
    {
        return true;
    }

    public function changeStatus(User $user, Procedure $procedure): bool
    {
        return true;
    }

    public function linkConsent(User $user, Procedure $procedure): bool
    {
        return true;
    }

    public function addAttachment(User $user, Procedure $procedure): bool
    {
        return true;
    }
}
