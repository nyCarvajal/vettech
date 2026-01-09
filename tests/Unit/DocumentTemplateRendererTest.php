<?php

namespace Tests\Unit;

use App\Services\DocumentTemplateRenderer;
use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class DocumentTemplateRendererTest extends TestCase
{
    public function test_it_replaces_owner_and_tutor_owner_tokens(): void
    {
        $renderer = new DocumentTemplateRenderer(new HtmlSanitizer());

        $html = '<p>Hola tutorowner.first_name, tel:owner.phone.</p>';
        $context = [
            'owner' => [
                'name' => 'Laura Gómez',
                'first_name' => 'Laura Gómez',
                'phone' => '300123',
            ],
        ];

        $rendered = $renderer->render($html, $context);

        $this->assertSame('<p>Hola Laura Gómez, tel:300123.</p>', $rendered);
    }

    public function test_it_supports_braced_tokens_and_missing_values(): void
    {
        $renderer = new DocumentTemplateRenderer(new HtmlSanitizer());

        $html = '<p>{{ owner.first_name }} {{ owner.last_name }}</p>';
        $context = [
            'owner' => [
                'first_name' => 'Mario',
            ],
        ];

        $rendered = $renderer->render($html, $context);

        $this->assertSame('<p>Mario </p>', $rendered);
    }

    public function test_it_sanitizes_html_and_preserves_surrounding_text(): void
    {
        $renderer = new DocumentTemplateRenderer(new HtmlSanitizer());

        $html = '<script>alert(1)</script><p>Contacto: owner.phone!</p>';
        $context = [
            'owner' => [
                'phone' => '555-123',
            ],
        ];

        $rendered = $renderer->render($html, $context);

        $this->assertSame('<p>Contacto: 555-123!</p>', $rendered);
    }
}
