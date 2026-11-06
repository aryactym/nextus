<?php

namespace App\Domain\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage courses');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array', 'min:1'],
            'title.id' => ['required', 'string', 'max:255'],
            'title.en' => ['sometimes', 'string', 'max:255'],
            'type' => ['required', 'in:video,text,pdf,quiz'],
            'content' => ['nullable', 'array'],
            'content.id' => ['sometimes', 'nullable', 'string'],
            'content.en' => ['sometimes', 'nullable', 'string'],
            'video_url' => ['exclude_unless:type,video', 'required_if:type,video', 'url', 'max:2048'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'is_preview' => ['boolean'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'file' => ['exclude_unless:type,pdf', 'required_if:type,pdf', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
