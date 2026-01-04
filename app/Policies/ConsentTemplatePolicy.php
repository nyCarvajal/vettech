<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ConsentTemplate;

class ConsentTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ConsentTemplate $template): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ConsentTemplate $template): bool
    {
        return true;
    }

    public function delete(User $user, ConsentTemplate $template): bool
    {
        return true;
    }
}
