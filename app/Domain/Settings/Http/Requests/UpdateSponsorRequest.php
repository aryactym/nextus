<?php

declare(strict_types=1);

namespace App\Domain\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSponsorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('manage settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'logo' => ['sometimes', 'nullable', 'file', 'image', 'max:2048'],
            'sort' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama sponsor',
            'url' => 'url situs',
            'logo' => 'logo sponsor',
            'sort' => 'urutan tampilan',
            'is_active' => 'status aktif',
        ];
    }
}
