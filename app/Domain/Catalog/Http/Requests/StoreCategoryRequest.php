<?php

namespace App\Domain\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage categories');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array', 'min:1'],
            'name.id' => ['required', 'string', 'max:100'],
            'name.en' => ['sometimes', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100',
                Rule::unique('categories', 'slug')
                    ->where('organization_id', organization_id())],
            'sort' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
