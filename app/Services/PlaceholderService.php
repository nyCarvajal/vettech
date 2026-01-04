<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PlaceholderService
{
    public function availablePlaceholders(): array
    {
        return [
            'owner.first_name' => ['label' => 'Nombre del tutor', 'example' => 'Juan'],
            'owner.last_name' => ['label' => 'Apellido del tutor', 'example' => 'Pérez'],
            'owner.full_name' => ['label' => 'Tutor nombre completo', 'example' => 'Juan Pérez'],
            'owner.document' => ['label' => 'Documento tutor', 'example' => 'CC 123'],
            'owner.phone' => ['label' => 'Teléfono tutor', 'example' => '+57 300123'],
            'owner.email' => ['label' => 'Email tutor', 'example' => 'correo@ejemplo.com'],
            'owner.address' => ['label' => 'Dirección tutor', 'example' => 'Calle 123'],
            'owner.city' => ['label' => 'Ciudad tutor', 'example' => 'Bogotá'],
            'pet.name' => ['label' => 'Nombre paciente', 'example' => 'Firulais'],
            'pet.species' => ['label' => 'Especie', 'example' => 'Canino'],
            'pet.breed' => ['label' => 'Raza', 'example' => 'Labrador'],
            'pet.sex' => ['label' => 'Sexo', 'example' => 'Macho'],
            'pet.age' => ['label' => 'Edad', 'example' => '5 años'],
            'pet.weight' => ['label' => 'Peso', 'example' => '12 kg'],
            'pet.color' => ['label' => 'Color', 'example' => 'Dorado'],
            'pet.microchip' => ['label' => 'Microchip', 'example' => '123-456'],
            'clinic.name' => ['label' => 'Clínica', 'example' => 'Clínica Vet'],
            'clinic.nit' => ['label' => 'NIT', 'example' => '900123'],
            'clinic.address' => ['label' => 'Dirección clínica', 'example' => 'Av 123'],
            'clinic.phone' => ['label' => 'Teléfono clínica', 'example' => '+57 123'],
            'vet.name' => ['label' => 'Veterinario', 'example' => 'Dra. López'],
            'vet.license' => ['label' => 'Licencia', 'example' => 'LIC-123'],
            'now.date' => ['label' => 'Fecha actual', 'example' => now()->format('Y-m-d')],
            'now.datetime' => ['label' => 'Fecha y hora', 'example' => now()->format('Y-m-d H:i')],
            'custom.field_1' => ['label' => 'Campo personalizado 1', 'example' => 'Valor libre'],
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
