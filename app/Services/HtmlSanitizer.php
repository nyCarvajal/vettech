<?php

namespace App\Services;

class HtmlSanitizer
{
    protected array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'ul', 'ol', 'li', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'table', 'thead', 'tbody', 'tr', 'th', 'td'
    ];

    public function sanitize(string $html): string
    {
        $allowed = '<' . implode('><', $this->allowedTags) . '>';
        return strip_tags($html, $allowed);
    }
}
