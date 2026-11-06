<?php

namespace App\Domain\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage categories');
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => ['sometimes', 'required', 'array', 'min:1'],
            'name.id' => ['sometimes', 'required', 'string', 'max:100'],
            'name.en' => ['sometimes', 'string', 'max:100'],
            'slug' => ['sometimes', 'required', 'string', 'max:100',
                'unique:categories,slug,'.$category->id.',id,organization_id,'.organization_id()],
            'sort' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}
