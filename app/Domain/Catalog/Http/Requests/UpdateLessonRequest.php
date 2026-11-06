<?php

namespace App\Domain\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage courses');
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'array', 'min:1'],
            'title.id' => ['sometimes', 'required', 'string', 'max:255'],
            'title.en' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'in:video,text,pdf,quiz'],
            'content' => ['sometimes', 'nullable', 'array'],
            'content.id' => ['sometimes', 'nullable', 'string'],
            'content.en' => ['sometimes', 'nullable', 'string'],
            'video_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'duration' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_preview' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'file' => ['sometimes', 'nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
