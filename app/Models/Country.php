<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $primaryKey = 'code2';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code2', 'name_es', 'name_en'];
}
