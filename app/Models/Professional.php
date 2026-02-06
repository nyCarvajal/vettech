<?php

namespace App\Models;

class Professional extends BaseModel
{
    protected $table = 'usuarios';

    public $timestamps = false;

    public function getNameAttribute(): ?string
    {
        $firstName = $this->nombres ?? $this->nombre ?? '';
        $name = trim($firstName . ' ' . ($this->apellidos ?? ''));

        return $name !== '' ? $name : null;
    }
}
