<?php

namespace Tests\Feature;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class HistoriaClinicaEcopTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('database.connections.mysql', Config::get('database.connections.sqlite'));
        Config::set('database.connections.tenant', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Artisan::call('migrate');
        Artisan::call('migrate', ['--database' => 'tenant']);
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => database_path('migrations/tenant'),
            '--realpath' => true,
        ]);
    }

    /** @test */
    public function it_persists_ecop_fields_in_historia_clinica(): void
    {
        $paciente = Paciente::create([
            'nombres' => 'Luna',
            'apellidos' => 'Díaz',
        ]);

        $historia = HistoriaClinica::create([
            'paciente_id' => $paciente->id,
            'estado' => 'borrador',
            'temperatura' => 38.5,
            'peso' => 12.4,
            'trc' => '2 seg',
            'mucosas' => 'Rosadas',
            'hidratacion' => 'Adecuada',
            'condicion_corporal' => '3/5',
            'frecuencia_cardiaca' => 120,
            'frecuencia_respiratoria' => 24,
            'estado_mental' => 'Alerta',
            'postura' => 'Normal',
            'marcha' => 'Sin claudicación',
            'dolor' => 'No evidente',
            'examen_ojos' => 'Sin hallazgos',
            'examen_oidos' => 'Limpios',
            'examen_boca' => 'Encías rosadas',
            'examen_ganglios' => 'No aumentados',
        ]);

        $this->assertDatabaseHas('historias_clinicas', [
            'id' => $historia->id,
            'temperatura' => 38.5,
            'trc' => '2 seg',
            'mucosas' => 'Rosadas',
            'estado_mental' => 'Alerta',
            'examen_ojos' => 'Sin hallazgos',
        ], 'tenant');
    }
}
