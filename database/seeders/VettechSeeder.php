<?php

namespace Database\Seeders;

use App\Models\Cage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ShiftDefinition;
use Illuminate\Database\Seeder;

class VettechSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['inventory.adjust', 'Ajustar inventario'],
            ['inventory.batch.manage', 'Gestionar lotes'],
            ['inventory.dispense', 'Dispensar medicamentos'],
            ['sales.discount', 'Aplicar descuentos'],
            ['sales.void', 'Anular ventas'],
            ['cash.open', 'Abrir caja'],
            ['cash.close', 'Cerrar caja'],
            ['cash.expense', 'Registrar egresos'],
            ['hospital.admit', 'Admitir paciente'],
            ['hospital.discharge', 'Dar de alta'],
            ['hospital.task.create', 'Crear tareas'],
        ];

        foreach ($permissions as [$name, $label]) {
            Permission::firstOrCreate(['name' => $name], ['label' => $label]);
        }

        $role = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrador']);
        $role->permissions()->sync(Permission::pluck('id'));

        if (ShiftDefinition::count() === 0) {
            ShiftDefinition::insert([
                ['name' => '07-15', 'start_time' => '07:00:00', 'end_time' => '15:00:00', 'active' => true],
                ['name' => '15-23', 'start_time' => '15:00:00', 'end_time' => '23:00:00', 'active' => true],
                ['name' => '23-07', 'start_time' => '23:00:00', 'end_time' => '07:00:00', 'active' => true],
            ]);
        }

        if (Cage::count() === 0) {
            $cages = [];
            for ($i = 1; $i <= 10; $i++) {
                $cages[] = ['name' => 'Jaula ' . $i, 'location' => 'Sala principal', 'active' => true];
            }
            Cage::insert($cages);
        }
    }
}
