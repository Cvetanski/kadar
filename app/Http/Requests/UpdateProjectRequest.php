<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('project'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'budget_negotiable' => ['nullable', 'boolean'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'country_id' => ['nullable', 'exists:countries,id', 'required_unless:remote_ok,1'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'remote_ok' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get the custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => __('Внеси наслов на проектот.'),
            'description.required' => __('Внеси опис на проектот.'),
            'category_ids.required' => __('Избери барем 1 категорија.'),
            'category_ids.min' => __('Избери барем 1 категорија.'),
            'budget_max.gte' => __('Буџетот до мора да биде поголем или еднаков на буџетот од.'),
            'deadline.after_or_equal' => __('Рокот не може да биде во минатото.'),
            'country_id.required_unless' => __('Избери земја, освен ако проектот е remote.'),
        ];
    }
}
