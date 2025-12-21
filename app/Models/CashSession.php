<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id', 'opened_by', 'opened_at', 'closed_at', 'opening_amount', 'closing_amount_expected', 'closing_amount_counted', 'status', 'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function register()
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }
}
