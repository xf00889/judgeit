<?php

namespace App\Http\Requests;

use App\Models\Criterion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ScoreSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the controller policy check
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'scores' => 'required|array|min:1',
            'scores.*.contestant_id' => 'required|integer|exists:contestants,id',
            'scores.*.criterion_id' => 'required|integer|exists:criteria,id',
            'scores.*.score_value' => 'nullable|numeric|min:0|max:100',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // No special preparation needed - just use the data as-is
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'scores.required' => 'At least one score must be provided.',
            'scores.array' => 'Scores must be provided in the correct format.',
            'scores.*.contestant_id.required' => 'Contestant ID is required for each score.',
            'scores.*.contestant_id.exists' => 'Invalid contestant selected.',
            'scores.*.criterion_id.required' => 'Criterion ID is required for each score.',
            'scores.*.criterion_id.exists' => 'Invalid criterion selected.',
            'scores.*.score_value.required' => 'Score value is required for each criterion.',
            'scores.*.score_value.numeric' => 'Score must be a number.',
            'scores.*.score_value.min' => 'Score cannot be negative.',
            'scores.*.score_value.max' => 'Score cannot exceed 100.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $scores = $this->input('scores', []);
            
            \Log::info('Validating scores', ['scores_count' => count($scores)]);
            
            // Validate each score
            foreach ($scores as $index => $score) {
                \Log::info("Score {$index}", ['score' => $score]);
                
                // Check if score_value is missing or null
                if (!isset($score['score_value']) || $score['score_value'] === null || $score['score_value'] === '') {
                    $validator->errors()->add(
                        "scores.{$index}.score_value",
                        "Score value is required for each criterion."
                    );
                    continue;
                }
                
                // Validate against criterion's max score
                if (isset($score['criterion_id'])) {
                    $criterion = Criterion::find($score['criterion_id']);
                    
                    \Log::info("Criterion lookup", [
                        'criterion_id' => $score['criterion_id'],
                        'found' => $criterion ? true : false,
                        'percentage' => $criterion ? $criterion->percentage : null,
                    ]);
                    
                    if ($criterion) {
                        $maxScore = (float)$criterion->max_score;
                        $scoreValue = (float)$score['score_value'];
                        
                        \Log::info("Score comparison", [
                            'score_value' => $scoreValue,
                            'max_score' => $maxScore,
                            'exceeds' => $scoreValue > $maxScore,
                        ]);
                        
                        if ($scoreValue > $maxScore) {
                            $validator->errors()->add(
                                "scores.{$index}.score_value",
                                "Score for {$criterion->name} cannot exceed {$maxScore}."
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'scores.*.score_value' => 'score',
            'scores.*.contestant_id' => 'contestant',
            'scores.*.criterion_id' => 'criterion',
        ];
    }
}
