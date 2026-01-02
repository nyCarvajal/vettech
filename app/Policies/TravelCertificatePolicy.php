<?php

namespace App\Policies;

use App\Models\TravelCertificate;
use App\Models\User;

class TravelCertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TravelCertificate $certificate): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TravelCertificate $certificate): bool
    {
        return $certificate->status === 'draft';
    }

    public function delete(User $user, TravelCertificate $certificate): bool
    {
        return $certificate->status === 'draft';
    }

    public function issue(User $user, TravelCertificate $certificate): bool
    {
        return $certificate->status === 'draft';
    }

    public function cancel(User $user, TravelCertificate $certificate): bool
    {
        return $certificate->status !== 'canceled';
    }

    public function exportPdf(User $user, TravelCertificate $certificate): bool
    {
        return true;
    }

    public function duplicate(User $user, TravelCertificate $certificate): bool
    {
        return true;
    }
}
