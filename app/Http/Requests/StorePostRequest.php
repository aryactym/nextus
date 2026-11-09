<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage blog') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:posts,slug', 'regex:/^[a-z0-9-]+$/'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
        ];
    }
}
