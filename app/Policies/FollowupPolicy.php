<?php

namespace App\Policies;

use App\Models\Followup;
use App\Models\User;

class FollowupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Followup $followup): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Followup $followup): bool
    {
        return true;
    }

    public function delete(User $user, Followup $followup): bool
    {
        return true;
    }

    public function addAttachment(User $user, Followup $followup): bool
    {
        return true;
    }
}
