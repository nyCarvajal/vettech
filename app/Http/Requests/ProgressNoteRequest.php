<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgressNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'shift' => ['nullable', Rule::in(['manana', 'tarde', 'noche'])],
            'logged_at' => ['required', 'date'],
            'author_id' => ['required', 'exists:users,id'],
        ];
    }
}
