<?php

namespace App\Services;

class HtmlSanitizer
{
    protected array $allowedTags = [
        'p', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'table', 'thead', 'tbody', 'tr', 'th', 'td'
    ];

    public function sanitize(string $html): string
    {
        $allowed = '<' . implode('><', $this->allowedTags) . '>';
        return strip_tags($html, $allowed);
    }
}
