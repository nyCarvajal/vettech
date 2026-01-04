<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClinicalAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'min:3', 'max:60'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => [
                'required',
                'file',
                'max:10240',
                'mimetypes:image/jpeg,image/png,image/webp,application/pdf,video/mp4,video/webm,video/quicktime',
                Rule::mimes(['jpg', 'jpeg', 'png', 'webp', 'pdf', 'mp4', 'webm', 'mov']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'files.*.max' => 'Cada archivo debe pesar máximo 10MB.',
            'files.*.mimetypes' => 'Tipo de archivo no permitido.',
            'files.*.mimes' => 'Extensión de archivo no permitida.',
        ];
    }

    public function sanitizedTitle(): string
    {
        $raw = $this->input('titulo', '');
        $ascii = Str::ascii($raw);
        $clean = preg_replace('/[^A-Za-z0-9_\-\s]+/', '', $ascii ?? '');
        $clean = trim(preg_replace('/\s+/', ' ', $clean ?? ''));

        $slug = strtolower(str_replace(' ', '-', $clean));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = preg_replace('/_+/', '_', $slug);

        if ($slug === '' || strlen($slug) < 3) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'titulo' => 'El título no es válido después de sanitizar. Usa letras, números, guion y guion bajo.',
            ]);
        }

        return $slug;
    }

    public function fileTypeFromMime(string $mime): string
    {
        return match (true) {
            str_starts_with($mime, 'image/') => 'image',
            $mime === 'application/pdf' => 'pdf',
            str_starts_with($mime, 'video/') => 'video',
            default => 'pdf',
        };
    }
}
