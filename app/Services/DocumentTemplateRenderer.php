<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DocumentTemplateRenderer
{
    protected string $missingValue = '';

    public function __construct(private HtmlSanitizer $sanitizer)
    {
    }

    public function render(string $html, array $context): string
    {
        $sanitizedHtml = $this->sanitizer->sanitize($html);
        $tokenMap = $this->buildTokenMap($context);

        if ($tokenMap === []) {
            return $sanitizedHtml;
        }

        $tokenPattern = implode('|', array_map(
            fn ($token) => preg_quote($token, '/'),
            array_keys($tokenMap)
        ));

        $pattern = '/\{\{\s*(?P<braced>' . $tokenPattern . ')\s*\}\}|(?<![A-Za-z0-9_\.])(?P<bare>' . $tokenPattern . ')(?![A-Za-z0-9_\.])/i';

        return preg_replace_callback($pattern, function (array $matches) use ($tokenMap) {
            $token = strtolower($matches['braced'] ?: $matches['bare']);
            $value = $tokenMap[$token] ?? $this->missingValue;

            return e($this->stringifyValue($value));
        }, $sanitizedHtml);
    }

    private function buildTokenMap(array $context): array
    {
        $flattened = Arr::dot($context);
        $map = [];

        foreach ($flattened as $key => $value) {
            $normalizedKey = strtolower($key);
            $map[$normalizedKey] = $value;

            if (Str::startsWith($normalizedKey, 'owner.')) {
                $aliasKey = 'tutorowner.' . Str::after($normalizedKey, 'owner.');
                $map[$aliasKey] = $value;
            }

            if (Str::startsWith($normalizedKey, 'tutorowner.')) {
                $aliasKey = 'owner.' . Str::after($normalizedKey, 'tutorowner.');
                $map[$aliasKey] = $value;
            }
        }

        return $map;
    }

    private function stringifyValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }
}
