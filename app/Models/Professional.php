<?php

namespace App\Models;

class Professional extends BaseModel
{
    protected $connection = 'mysql';

    protected $table = 'usuarios';

    public $timestamps = false;

    public function getConnectionName()
    {
        return 'mysql';
    }

    public function getNameAttribute(): ?string
    {
        $firstName = $this->nombres ?? $this->nombre ?? '';
        $name = trim($firstName . ' ' . ($this->apellidos ?? ''));

        return $name !== '' ? $name : null;
    }
}
