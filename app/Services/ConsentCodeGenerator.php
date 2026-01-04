<?php

namespace App\Services;

use App\Models\ConsentDocument;

class ConsentCodeGenerator
{
    public function generate(?int $tenantId = null): string
    {
        $query = ConsentDocument::query();

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $lastId = (int) $query->max('id');
        $next = $lastId + 1;

        return 'CI-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
