<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ConsentPublicLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'consent_document_id',
        'token_hash',
        'expires_at',
        'max_uses',
        'uses',
        'last_used_at',
        'revoked_at',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function document()
    {
        return $this->belongsTo(ConsentDocument::class, 'consent_document_id');
    }

    public function isValid(): bool
    {
        if ($this->revoked_at) {
            return false;
        }

        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return false;
        }

        if ($this->uses >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
