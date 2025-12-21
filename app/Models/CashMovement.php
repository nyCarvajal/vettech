<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_session_id', 'type', 'amount', 'method', 'reason', 'ref_entity', 'ref_id', 'created_by',
    ];

    public function session()
    {
        return $this->belongsTo(CashSession::class, 'cash_session_id');
    }
}
