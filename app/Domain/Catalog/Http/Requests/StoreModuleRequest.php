<?php

namespace App\Domain\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
