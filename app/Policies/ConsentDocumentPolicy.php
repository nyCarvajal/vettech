<?php

namespace App\Policies;

use App\Models\ConsentDocument;
use App\Models\User;

class ConsentDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ConsentDocument $document): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function cancel(User $user, ConsentDocument $document): bool
    {
        return true;
    }

    public function pdf(User $user, ConsentDocument $document): bool
    {
        return true;
    }

    public function sign(User $user, ConsentDocument $document): bool
    {
        return true;
    }

    public function createPublicLink(User $user, ConsentDocument $document): bool
    {
        return true;
    }
}
