<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'clinic_id'];

    public function sessions()
    {
        return $this->hasMany(CashSession::class);
    }
}
