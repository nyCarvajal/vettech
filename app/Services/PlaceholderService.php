<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PlaceholderService
{
    public function availablePlaceholders(): array
    {
        return [
            'owner.first_name' => ['label' => 'Nombre', 'example' => 'Juan', 'display' => 'Nombre.tutor'],
            'owner.last_name' => ['label' => 'Apellido', 'example' => 'Pérez', 'display' => 'Apellido.tutor'],
            'owner.full_name' => ['label' => 'Nombre completo', 'example' => 'Juan Pérez', 'display' => 'NombreCompleto.tutor'],
            'owner.document' => ['label' => 'Documento', 'example' => 'CC 123', 'display' => 'Documento.tutor'],
            'owner.phone' => ['label' => 'Teléfono', 'example' => '+57 300123', 'display' => 'Telefono.tutor'],
            'owner.email' => ['label' => 'Correo', 'example' => 'correo@ejemplo.com', 'display' => 'Correo.tutor'],
            'owner.address' => ['label' => 'Dirección', 'example' => 'Calle 123', 'display' => 'Direccion.tutor'],
            'owner.city' => ['label' => 'Ciudad', 'example' => 'Bogotá', 'display' => 'Ciudad.tutor'],
            'pet.name' => ['label' => 'Nombre', 'example' => 'Firulais', 'display' => 'Nombre.paciente'],
            'pet.species' => ['label' => 'Especie', 'example' => 'Canino', 'display' => 'Especie.paciente'],
            'pet.breed' => ['label' => 'Raza', 'example' => 'Labrador', 'display' => 'Raza.paciente'],
            'pet.sex' => ['label' => 'Sexo', 'example' => 'Macho', 'display' => 'Sexo.paciente'],
            'pet.age' => ['label' => 'Edad', 'example' => '5 años', 'display' => 'Edad.paciente'],
            'pet.weight' => ['label' => 'Peso', 'example' => '12 kg', 'display' => 'Peso.paciente'],
            'pet.color' => ['label' => 'Color', 'example' => 'Dorado', 'display' => 'Color.paciente'],
            'pet.microchip' => ['label' => 'Microchip', 'example' => '123-456', 'display' => 'Microchip.paciente'],
            'clinic.name' => ['label' => 'Clínica', 'example' => 'Clínica Vet', 'display' => 'Clinica.clinica'],
            'clinic.nit' => ['label' => 'NIT', 'example' => '900123', 'display' => 'NIT.clinica'],
            'clinic.address' => ['label' => 'Dirección', 'example' => 'Av 123', 'display' => 'Direccion.clinica'],
            'clinic.phone' => ['label' => 'Teléfono', 'example' => '+57 123', 'display' => 'Telefono.clinica'],
            'vet.name' => ['label' => 'Veterinario', 'example' => 'Dra. López', 'display' => 'Nombre.veterinario'],
            'vet.license' => ['label' => 'Licencia', 'example' => 'LIC-123', 'display' => 'Licencia.veterinario'],
            'now.date' => ['label' => 'Fecha', 'example' => now()->format('Y-m-d'), 'display' => 'Fecha.actual'],
            'now.datetime' => ['label' => 'Fecha y hora', 'example' => now()->format('Y-m-d H:i'), 'display' => 'FechaHora.actual'],
            'custom.field_1' => ['label' => 'Campo 1', 'example' => 'Valor libre', 'display' => 'Campo1.personalizado'],
        ];
    }

    public function merge(string $templateHtml, array $context): string
    {
        $map = $this->flattenContext($context);
        $html = $templateHtml;

        preg_match_all('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', $html, $matches);
        foreach ($matches[1] as $placeholder) {
            $value = $map[$placeholder] ?? '';
            $html = str_replace('{{' . $placeholder . '}}', e($value), $html);
            $html = str_replace('{{ ' . $placeholder . ' }}', e($value), $html);
        }

        return $html;
    }

    public function validatePlaceholders(string $templateHtml, ?array $allowed = null): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', $templateHtml, $matches);
        $placeholders = array_unique($matches[1] ?? []);

        if (!empty($allowed)) {
            $invalid = array_diff($placeholders, $allowed);
            return array_values($invalid);
        }

        return [];
    }

    private function flattenContext(array $context): array
    {
        $flattened = [];

        $iterator = function ($value, $prefix = '') use (&$flattened, &$iterator) {
            if (is_array($value)) {
                foreach ($value as $key => $child) {
                    $iterator($child, $prefix === '' ? $key : $prefix . '.' . $key);
                }
            } else {
                $flattened[$prefix] = $value;
            }
        };

        foreach ($context as $key => $value) {
            $iterator($value, $key);
        }

        return $flattened;
    }
}
