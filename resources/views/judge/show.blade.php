@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <!-- Header Section -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
            <p class="text-gray-600 mt-2">Score Entry for Assigned Gender</p>
        </div>
        <a href="{{ route('judge.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Categories
        </a>
    </div>

    <!-- Progress Indicator -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Scoring Progress</h2>
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-gray-600">
                <span id="progress-text">0 of {{ $contestants->count() * count($criteria) }}</span> scores entered
            </span>
            <span class="text-sm font-semibold text-blue-600" id="progress-percentage">
                0%
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <!-- Error Messages -->
    @if(session('error'))
        <div class="bg-red-50 border-2 border-red-400 rounded-lg p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-2 border-red-400 rounded-lg p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800 mb-2">Please correct the following errors:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Already Submitted State -->
    @if($has_submitted)
        <div class="bg-blue-50 border-2 border-blue-400 rounded-lg p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-bold text-blue-800">
                        Scores Already Submitted
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>You have already submitted scores for this category. Scores cannot be modified after submission.</p>
                        <a href="{{ route('judge.index') }}" 
                           class="inline-flex items-center px-4 py-2 mt-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Return to Categories →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Scoring Form -->
        <form method="POST" action="{{ route('judge.categories.submit', $category) }}" id="judging-form">
            @csrf

            <!-- Score Sheet Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="bg-purple-600 px-6 py-4">
                    <h3 class="text-2xl font-bold text-white">Score Entry</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase">
                                    Contestant No.
                                </th>
                                
                                @foreach($criteria as $criterion)
                                    <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase">
                                        {{ $criterion->name }}
                                        <br>
                                        <span class="text-xs font-normal text-gray-500">({{ $criterion->percentage }}%)</span>
                                    </th>
                                @endforeach

                                <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-white uppercase bg-blue-600">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contestants as $contestantIdx => $contestant)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Contestant Number -->
                                    <td class="border-2 border-gray-300 px-4 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-2xl font-bold text-gray-900">#{{ $contestant->contestant_number }}</span>
                                            @if($contestant->name)
                                                <span class="text-sm text-gray-600 mt-1">{{ $contestant->name }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Criteria Score Inputs -->
                                    @foreach($criteria as $criterionIdx => $criterion)
                                        @php
                                            $scoreIndex = ($contestantIdx * count($criteria)) + $criterionIdx;
                                            $fieldName = "scores[{$scoreIndex}][score_value]";
                                            $oldValue = old($fieldName, $cachedScores[$scoreIndex]['score_value'] ?? '');
                                        @endphp
                                        <td class="border-2 border-gray-300 px-4 py-4 text-center">
                                            <input 
                                                type="hidden" 
                                                name="scores[{{ $scoreIndex }}][contestant_id]"
                                                value="{{ $contestant->id }}"
                                            >
                                            <input 
                                                type="hidden" 
                                                name="scores[{{ $scoreIndex }}][criterion_id]"
                                                value="{{ $criterion->id }}"
                                            >
                                            <div class="flex flex-col items-center">
                                                <input 
                                                    type="number" 
                                                    name="{{ $fieldName }}"
                                                    id="score_{{ $scoreIndex }}"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ $criterion->max_score }}"
                                                    required
                                                    data-max="{{ $criterion->max_score }}"
                                                    data-contestant="{{ $contestantIdx }}"
                                                    class="score-input w-32 px-3 py-2 text-lg font-semibold text-center border-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error($fieldName) border-red-500 bg-red-50 @else border-gray-300 @enderror"
                                                    placeholder="0.00"
                                                    value="{{ $oldValue }}"
                                                    aria-label="Score for {{ $criterion->name }}, Contestant {{ $contestant->contestant_number }}"
                                                >
                                                @error($fieldName)
                                                    <span class="text-xs text-red-600 mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                    @endforeach

                                    <!-- Total Column -->
                                    <td class="border-2 border-gray-300 px-4 py-4 text-center bg-blue-50">
                                        <span class="text-2xl font-bold text-blue-900" id="total_{{ $contestantIdx }}">
                                            0.00
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <button type="submit" id="submit-btn"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Submit All Scores
                </button>
            </div>
        </form>
    @endif
</div>

<!-- Confirmation Modal -->
@if(!$has_submitted)
<div id="confirmation-modal" class="hidden fixed inset-0 z-50" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="flex items-center justify-center min-h-screen p-4" onclick="event.target === this && closeConfirmationModal()">
        <div class="relative bg-white rounded-lg shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="bg-blue-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-white">Confirm Your Scores</h3>
                    <button type="button" onclick="closeConfirmationModal()" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(90vh-200px)] p-6">
                
                <!-- Warning Banner -->
                <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-yellow-800">Important Notice</h3>
                            <p class="text-sm text-yellow-700 mt-1">Once confirmed, these scores cannot be modified. Please review all entries carefully before proceeding.</p>
                        </div>
                    </div>
                </div>

                <!-- Preview Table -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase">
                                        Contestant No.
                                    </th>
                                    @foreach($criteria as $criterion)
                                        <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase">
                                            {{ $criterion->name }}
                                        </th>
                                    @endforeach
                                    <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-white uppercase bg-blue-600">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="preview-tbody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Confirmation Checkbox -->
                <div class="mt-6 bg-gray-50 border-2 border-gray-200 rounded-lg p-4">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            id="confirmation_checkbox"
                            class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <div>
                            <span class="block text-sm font-bold text-gray-900">
                                I confirm that all scores are accurate and final
                            </span>
                            <span class="block text-sm text-gray-600 mt-1">
                                I understand that once confirmed, these scores cannot be modified.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t-2 border-gray-200 flex items-center justify-end space-x-3">
                <button type="button" 
                        onclick="closeConfirmationModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="button" 
                        id="confirm-submit-btn"
                        onclick="confirmSubmission()"
                        disabled
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-400 cursor-not-allowed">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Confirm & Submit
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    /* Smooth transitions */
    .transition-colors {
        transition-property: background-color, border-color, color;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('judging-form');
        if (!form) return;

        const totalContestants = {{ $contestants->count() }};
        const criteriaCount = {{ count($criteria) }};
        const totalScores = totalContestants * criteriaCount;
        let isSubmitting = false;

        // Update progress indicators
        function updateProgress() {
            let filledScores = 0;
            const inputs = form.querySelectorAll('.score-input');
            
            inputs.forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    filledScores++;
                }
            });

            // Update progress text
            const progressText = document.getElementById('progress-text');
            if (progressText) {
                progressText.textContent = `${filledScores} of ${totalScores}`;
            }

            // Update progress bar
            const progressBar = document.getElementById('progress-bar');
            const progressPercentage = document.getElementById('progress-percentage');
            if (progressBar && progressPercentage) {
                const percentage = Math.round((filledScores / totalScores) * 100);
                progressBar.style.width = `${percentage}%`;
                progressPercentage.textContent = `${percentage}%`;
            }
        }

        // Calculate totals for each contestant
        function calculateTotals() {
            for (let i = 0; i < totalContestants; i++) {
                let total = 0;

                for (let j = 0; j < criteriaCount; j++) {
                    const scoreIndex = (i * criteriaCount) + j;
                    const input = document.getElementById(`score_${scoreIndex}`);
                    
                    if (input && input.value) {
                        const value = parseFloat(input.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    }
                }

                // Update total display
                const totalElement = document.getElementById(`total_${i}`);
                if (totalElement) {
                    totalElement.textContent = total.toFixed(2);
                }
            }

            updateProgress();
        }

        // Validate score input
        function validateScoreInput(input) {
            const value = parseFloat(input.value);
            const max = parseFloat(input.getAttribute('data-max'));
            
            // Remove all state classes
            input.classList.remove('border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50', 'border-gray-300');
            
            if (input.value === '') {
                // Empty state
                input.classList.add('border-gray-300');
            } else if (isNaN(value) || value < 0 || value > max) {
                // Invalid state
                input.classList.add('border-red-500', 'bg-red-50');
            } else {
                // Valid state
                input.classList.add('border-green-500', 'bg-green-50');
            }
        }

        // Listen to all score inputs
        const inputs = form.querySelectorAll('.score-input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                validateScoreInput(this);
                calculateTotals();
            });
            
            input.addEventListener('blur', function() {
                validateScoreInput(this);
            });

            // Auto-select on focus for faster editing
            input.addEventListener('focus', function() {
                this.select();
            });

            // Initial validation
            if (input.value) {
                validateScoreInput(input);
            }
        });

        // Initial calculation
        calculateTotals();

        // Handle form submission
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            // Check if this is the confirmation submission
            const confirmationNote = document.querySelector('input[name="confirmation_note"]');
            if (!confirmationNote || !confirmationNote.value) {
                e.preventDefault();
                showConfirmationModal();
                return false;
            }

            // Final submission with loading state
            isSubmitting = true;
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.className = 'inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-400 cursor-not-allowed';
                submitBtn.innerHTML = `
                    <svg class="animate-spin mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...
                `;
            }
        });
    });

    // Show confirmation modal
    function showConfirmationModal() {
        const form = document.getElementById('judging-form');
        const modal = document.getElementById('confirmation-modal');
        const tbody = document.getElementById('preview-tbody');
        
        if (!modal || !tbody) return;

        tbody.innerHTML = '';

        const contestants = @json($contestants);
        const criteria = @json($criteria);
        const criteriaCount = criteria.length;

        // Build preview rows
        contestants.forEach((contestant, contestantIdx) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors';
            
            // Contestant number
            const contestantCell = document.createElement('td');
            contestantCell.className = 'border-2 border-gray-300 px-4 py-4 text-center';
            contestantCell.innerHTML = `
                <div class="flex flex-col items-center">
                    <span class="text-2xl font-bold text-gray-900">#${contestant.contestant_number}</span>
                    ${contestant.name ? `<span class="text-sm text-gray-600 mt-1">${contestant.name}</span>` : ''}
                </div>
            `;
            row.appendChild(contestantCell);
            
            let total = 0;
            
            // Score cells
            criteria.forEach((criterion, criterionIdx) => {
                const scoreIndex = (contestantIdx * criteriaCount) + criterionIdx;
                const input = document.getElementById(`score_${scoreIndex}`);
                const scoreValue = input ? parseFloat(input.value) || 0 : 0;
                total += scoreValue;
                
                const scoreCell = document.createElement('td');
                scoreCell.className = 'border-2 border-gray-300 px-4 py-4 text-center';
                scoreCell.innerHTML = `<span class="text-lg font-semibold text-gray-900">${scoreValue.toFixed(2)}</span>`;
                row.appendChild(scoreCell);
            });
            
            // Total cell
            const totalCell = document.createElement('td');
            totalCell.className = 'border-2 border-gray-300 px-4 py-4 text-center bg-blue-50';
            totalCell.innerHTML = `<span class="text-2xl font-bold text-blue-900">${total.toFixed(2)}</span>`;
            row.appendChild(totalCell);
            
            tbody.appendChild(row);
        });

        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Setup checkbox
        const checkbox = document.getElementById('confirmation_checkbox');
        const submitBtn = document.getElementById('confirm-submit-btn');
        
        if (checkbox && submitBtn) {
            checkbox.checked = false;
            
            // Remove any existing listeners
            const newCheckbox = checkbox.cloneNode(true);
            checkbox.parentNode.replaceChild(newCheckbox, checkbox);
            
            newCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    submitBtn.disabled = false;
                    submitBtn.className = 'inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.className = 'inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-400 cursor-not-allowed';
                }
            });
        }
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmation-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            const checkbox = document.getElementById('confirmation_checkbox');
            if (checkbox) {
                checkbox.checked = false;
            }
        }
    }

    function confirmSubmission() {
        const checkbox = document.getElementById('confirmation_checkbox');
        
        if (!checkbox || !checkbox.checked) {
            alert('Please check the confirmation box before submitting.');
            return;
        }

        const form = document.getElementById('judging-form');
        const confirmInput = document.createElement('input');
        confirmInput.type = 'hidden';
        confirmInput.name = 'confirmation_note';
        confirmInput.value = 'Confirmed via checkbox';
        form.appendChild(confirmInput);

        form.submit();
    }
</script>
@endsection
