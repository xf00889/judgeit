<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OverallRankingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isTabulator();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'components' => 'required|array|min:1',
            'components.*.name' => 'required|string|max:255',
            'components.*.type' => 'required|in:fixed,category_percentage',
            'components.*.category_id' => 'nullable|exists:categories,id',
            'components.*.percentage' => 'required|numeric|min:0|max:100',
            'components.*.category_weights' => 'nullable|array',
            'components.*.category_weights.*.category_id' => 'required|exists:categories,id',
            'components.*.category_weights.*.percentage' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'components.required' => 'At least one component is required.',
            'components.*.name.required' => 'Each component must have a name.',
            'components.*.type.required' => 'Each component must have a type.',
            'components.*.type.in' => 'Type must be either "fixed" or "category_percentage".',
            'components.*.category_id.exists' => 'Selected category does not exist.',
            'components.*.percentage.required' => 'Each component must have a percentage.',
            'components.*.percentage.numeric' => 'Percentage must be a number.',
            'components.*.percentage.min' => 'Percentage must be at least 0.',
            'components.*.percentage.max' => 'Percentage cannot exceed 100.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $components = $this->input('components', []);
            $total = array_sum(array_column($components, 'percentage'));
            
            if (abs($total - 100) > 0.01) {
                $validator->errors()->add('components', 'Component percentages must sum to exactly 100%. Current total: ' . number_format($total, 2) . '%');
            }

            // Validate category weights sum to component percentage
            foreach ($components as $index => $component) {
                if (isset($component['type']) && $component['type'] === 'category_percentage' && isset($component['category_weights'])) {
                    $categoryWeightsTotal = array_sum(array_column($component['category_weights'], 'percentage'));
                    $componentPercentage = floatval($component['percentage'] ?? 0);
                    
                    if (abs($categoryWeightsTotal - $componentPercentage) > 0.01) {
                        $validator->errors()->add(
                            "components.{$index}.category_weights",
                            "Category weights for '{$component['name']}' must sum to {$componentPercentage}%. Current total: " . number_format($categoryWeightsTotal, 2) . '%'
                        );
                    }
                }
            }
        });
    }
}
