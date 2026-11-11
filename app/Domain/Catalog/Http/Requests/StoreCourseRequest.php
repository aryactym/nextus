<?php

namespace App\Domain\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
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
            'summary' => ['nullable', 'array'],
            'summary.id' => ['sometimes', 'string'],
            'summary.en' => ['sometimes', 'string'],
            'description' => ['nullable', 'array'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['required', 'string', 'max:255',
                Rule::unique('courses', 'slug')
                    ->where('organization_id', organization_id())],
            'level' => ['required', 'string', 'max:50'],
            'duration_minutes' => ['required', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'certificate_background' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'access_type' => ['required', 'in:free,donation,paid'],
            'certificate_type' => ['required', 'in:free,donation,paid'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'suggested_amounts' => ['nullable', 'array'],
            'allow_custom_amount' => ['boolean'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'passing_grade' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_featured' => ['sometimes', 'boolean'],
            'featured_sort' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published,archived'],
        ];
    }
}
