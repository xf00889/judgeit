<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*.name' => ['required', 'string', 'max:255'],
            'criteria.*.percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'criteria.required' => 'At least one criterion is required.',
            'criteria.min' => 'At least one criterion is required.',
            'criteria.*.name.required' => 'Each criterion must have a name.',
            'criteria.*.percentage.required' => 'Each criterion must have a percentage.',
            'criteria.*.percentage.numeric' => 'Each criterion percentage must be a number.',
        ];
    }

    /**
     * Validate that criteria percentages sum to 100%.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $criteria = $this->input('criteria', []);
            $total = 0;
            foreach ($criteria as $criterion) {
                $total += (float)($criterion['percentage'] ?? 0);
            }

            if (abs($total - 100) > 0.01) {
                $validator->errors()->add('criteria', 'Criteria percentages must sum to exactly 100%. Current total: ' . number_format($total, 2) . '%');
            }
        });
    }
}
