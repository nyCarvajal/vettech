<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsentTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'category',
        'body_html',
        'is_active',
        'allowed_placeholders',
        'required_signers',
        'requires_pet',
        'requires_owner',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allowed_placeholders' => 'array',
        'required_signers' => 'array',
        'is_active' => 'boolean',
        'requires_pet' => 'boolean',
        'requires_owner' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(ConsentDocument::class, 'template_id');
    }
}
