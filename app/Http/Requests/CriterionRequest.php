<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriterionRequest extends FormRequest
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
            'max_score' => ['required', 'integer', 'min:1'],
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
            'name.required' => 'The criterion name is required.',
            'max_score.required' => 'The maximum score is required.',
            'max_score.integer' => 'The maximum score must be a whole number.',
            'max_score.min' => 'The maximum score must be at least 1.',
        ];
    }
}
