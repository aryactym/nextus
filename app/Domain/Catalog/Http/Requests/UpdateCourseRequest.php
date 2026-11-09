<?php

namespace App\Domain\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage courses');
    }

    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'title' => ['sometimes', 'required', 'array', 'min:1'],
            'title.id' => ['sometimes', 'required', 'string', 'max:255'],
            'title.en' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'nullable', 'array'],
            'description' => ['sometimes', 'nullable', 'array'],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'slug' => ['sometimes', 'required', 'string', 'max:255',
                'unique:courses,slug,'.$course->id.',id,organization_id,'.organization_id()],
            'level' => ['sometimes', 'required', 'string', 'max:50'],
            'duration_minutes' => ['required', 'integer', 'min:0'],
            'thumbnail' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'certificate_background' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'access_type' => ['sometimes', 'required', 'in:free,donation,paid'],
            'certificate_type' => ['sometimes', 'required', 'in:free,donation,paid'],
            'min_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'suggested_amounts' => ['sometimes', 'nullable', 'array'],
            'allow_custom_amount' => ['sometimes', 'boolean'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'passing_grade' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['sometimes', 'required', 'in:draft,published,archived'],
        ];
    }
}
