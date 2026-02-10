@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Create Overall Ranking Formula</h2>
        <p class="text-sm text-gray-600 mt-1">Define how the final winner is calculated by combining finalized categories with different weights.</p>
    </div>

    <form action="{{ route('admin.overall-rankings.store') }}" method="POST" class="px-6 py-4" id="overall-ranking-form">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                Ranking Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                placeholder="e.g., King & Queen Overall Ranking"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" 
                required>
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                Description
            </label>
            <textarea name="description" id="description" rows="2" 
                placeholder="Optional description of this ranking formula"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Category Components <span class="text-red-500">*</span>
            </label>
            <p class="text-gray-500 text-xs italic mb-3">Select categories and assign weights. Percentages must sum to 100%.</p>
            
            @if($categories->isEmpty())
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                    <p class="text-yellow-800 text-sm">No categories available. Please create at least one category first.</p>
                </div>
            @else
                <div class="overflow-x-auto mb-4">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold">Name</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold">Type</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold">Categories</th>
                                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold">Percentage (%)</th>
                                <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="components-table-body">
                            <!-- Rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                    <p class="text-sm text-blue-800">
                        Total Percentage: <span id="total-percentage" class="font-bold">0</span>%
                        <span id="percentage-status" class="ml-2"></span>
                    </p>
                </div>

                <button type="button" id="add-component-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    + Add Category Component
                </button>
            @endif

            @error('components')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" {{ $categories->isEmpty() ? 'disabled' : '' }}>
                Create Overall Ranking
            </button>
            <a href="{{ route('admin.overall-rankings.index') }}" class="text-gray-600 hover:text-gray-800">
                Cancel
            </a>
        </div>
    </form>
</div>

@if($categories->isNotEmpty())
<!-- Cache busting comment: v2.1 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = document.querySelectorAll('.component-row').length;
        const categories = @json($categories);

        function updateTotalPercentage() {
            const percentageInputs = document.querySelectorAll('.component-percentage');
            let total = 0;
            percentageInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });

            const totalSpan = document.getElementById('total-percentage');
            const statusSpan = document.getElementById('percentage-status');
            totalSpan.textContent = total.toFixed(2);

            if (total === 100) {
                statusSpan.innerHTML = '<span class="text-green-600 font-semibold">✓ Valid</span>';
            } else if (total > 100) {
                statusSpan.innerHTML = '<span class="text-red-600 font-semibold">✗ Exceeds 100%</span>';
            } else {
                statusSpan.innerHTML = '<span class="text-orange-600 font-semibold">⚠ Incomplete</span>';
            }
        }

        function createCategoryWeightsUI(componentIndex, container, componentPercentage = 0) {
            container.innerHTML = `
                <div class="space-y-2">
                    <div class="category-weights-list"></div>
                    <button type="button" class="add-category-weight-btn bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-2 rounded">
                        + Add Category
                    </button>
                    <div class="text-xs mt-1">
                        <span class="text-gray-600">Total: </span>
                        <span class="category-weights-total font-semibold">0.00</span>%
                        <span class="category-weights-status ml-1"></span>
                    </div>
                </div>
            `;

            const addBtn = container.querySelector('.add-category-weight-btn');
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addCategoryWeight(componentIndex, container, componentPercentage);
            });
        }

        function addCategoryWeight(componentIndex, container, componentPercentage) {
            const list = container.querySelector('.category-weights-list');
            const weightIndex = list.querySelectorAll('.category-weight-item').length;
            
            // Get the row to access the percentage input
            const row = container.closest('tr');
            const percentageInput = row.querySelector('.component-percentage');
            
            let categoryOptions = '<option value="">Select category...</option>';
            categories.forEach(cat => {
                categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
            });

            const weightItem = document.createElement('div');
            weightItem.className = 'category-weight-item flex gap-2 items-center mb-2';
            weightItem.innerHTML = `
                <select name="components[${componentIndex}][category_weights][${weightIndex}][category_id]" 
                    class="flex-1 px-2 py-1 border rounded text-sm" required>
                    ${categoryOptions}
                </select>
                <input type="number" 
                    name="components[${componentIndex}][category_weights][${weightIndex}][percentage]" 
                    step="0.01" min="0" max="100"
                    placeholder="0.00"
                    class="w-20 px-2 py-1 border rounded text-sm category-weight-percentage"
                    required>
                <button type="button" class="remove-category-weight-btn bg-red-500 hover:bg-red-600 text-white text-xs py-1 px-2 rounded">
                    ✕
                </button>
            `;
            
            list.appendChild(weightItem);

            const removeBtn = weightItem.querySelector('.remove-category-weight-btn');
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                weightItem.remove();
                // Get current value from input instead of using closure variable
                updateCategoryWeightsTotal(container, percentageInput.value);
            });

            const categoryWeightInput = weightItem.querySelector('.category-weight-percentage');
            categoryWeightInput.addEventListener('input', function() {
                // Get current value from input instead of using closure variable
                updateCategoryWeightsTotal(container, percentageInput.value);
            });

            // Get current value from input instead of using closure variable
            updateCategoryWeightsTotal(container, percentageInput.value);
        }

        function updateCategoryWeightsTotal(container, componentPercentage) {
            console.log('=== updateCategoryWeightsTotal CALLED ===');
            console.log('Stack trace:', new Error().stack);
            
            const percentageInputs = container.querySelectorAll('.category-weight-percentage');
            let total = 0;
            percentageInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            const totalSpan = container.querySelector('.category-weights-total');
            const statusSpan = container.querySelector('.category-weights-status');
            
            console.log('Before setting text:', {
                currentStatusHTML: statusSpan.innerHTML,
                totalSpanText: totalSpan.textContent
            });
            
            totalSpan.textContent = total.toFixed(2);

            const target = parseFloat(componentPercentage) || 0;
            const difference = Math.abs(total - target);
            
            // Debug logging
            console.log('Category Weights Debug:', {
                total: total,
                target: target,
                difference: difference,
                tolerance: 0.01,
                isWithinTolerance: difference < 0.01,
                targetGreaterThanZero: target > 0
            });
            
            if (difference < 0.01 && target > 0) {
                statusSpan.innerHTML = '<span class="text-green-600">✓</span>';
                console.log('Status: Valid (✓) - Condition 1 matched');
            } else if (total > target && difference >= 0.01) {
                statusSpan.innerHTML = '<span class="text-red-600">✗ Exceeds</span>';
                console.log('Status: Exceeds (✗) - Condition 2 matched', {
                    'total > target': total > target,
                    'difference >= 0.01': difference >= 0.01,
                    'both conditions': (total > target && difference >= 0.01)
                });
            } else if (target > 0 && total < target) {
                statusSpan.innerHTML = '<span class="text-orange-600">⚠</span>';
                console.log('Status: Incomplete (⚠) - Condition 3 matched');
            } else {
                statusSpan.innerHTML = '';
                console.log('Status: Empty - No conditions matched');
            }
            
            console.log('After setting status:', {
                newStatusHTML: statusSpan.innerHTML
            });
            console.log('=== END updateCategoryWeightsTotal ===\n');
        }

        function addComponentRow() {
            const tbody = document.getElementById('components-table-body');
            const newRow = document.createElement('tr');
            newRow.className = 'component-row';
            
            newRow.innerHTML = `
                <td class="border border-gray-300 px-4 py-2">
                    <input type="text" name="components[${rowCount}][name]" 
                        placeholder="e.g., Beauty Score"
                        class="w-full px-2 py-1 border rounded"
                        required>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <select name="components[${rowCount}][type]" class="w-full px-2 py-1 border rounded component-type" required>
                        <option value="">Select type...</option>
                        <option value="fixed">Fixed</option>
                        <option value="category_percentage">Category Percentage</option>
                    </select>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <div class="category-weights-container">
                        <p class="text-gray-500 text-sm italic category-placeholder">Select type first</p>
                    </div>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="number" name="components[${rowCount}][percentage]" 
                        step="0.01" min="0" max="100"
                        placeholder="0.00"
                        class="w-full px-2 py-1 border rounded component-percentage"
                        required>
                </td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                    <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm remove-component-btn">
                        Remove
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            
            const typeSelect = newRow.querySelector('.component-type');
            const container = newRow.querySelector('.category-weights-container');
            const percentageInput = newRow.querySelector('.component-percentage');

            typeSelect.addEventListener('change', function() {
                if (this.value === 'category_percentage') {
                    const componentPercentage = parseFloat(percentageInput.value) || 0;
                    createCategoryWeightsUI(rowCount, container, componentPercentage);
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-sm italic">N/A (Fixed type)</p>';
                }
            });

            percentageInput.addEventListener('input', function() {
                updateTotalPercentage();
                if (typeSelect.value === 'category_percentage') {
                    updateCategoryWeightsTotal(container, this.value);
                }
            });

            const removeBtn = newRow.querySelector('.remove-component-btn');
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                newRow.remove();
                updateTotalPercentage();
            });

            rowCount++;
            updateTotalPercentage();
        }

        document.getElementById('add-component-btn').addEventListener('click', function(e) {
            e.preventDefault();
            addComponentRow();
        });

        document.querySelectorAll('.remove-component-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('tr').remove();
                updateTotalPercentage();
            });
        });

        document.querySelectorAll('.component-percentage').forEach(input => {
            input.addEventListener('input', updateTotalPercentage);
        });

        document.querySelectorAll('.component-type').forEach((select, index) => {
            select.addEventListener('change', function() {
                const row = this.closest('tr');
                const container = row.querySelector('.category-weights-container');
                const percentageInput = row.querySelector('.component-percentage');
                
                if (this.value === 'category_percentage') {
                    const componentPercentage = parseFloat(percentageInput.value) || 0;
                    createCategoryWeightsUI(index, container, componentPercentage);
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-sm italic">N/A (Fixed type)</p>';
                }
            });

            const row = select.closest('tr');
            const percentageInput = row.querySelector('.component-percentage');
            const container = row.querySelector('.category-weights-container');
            
            percentageInput.addEventListener('input', function() {
                if (select.value === 'category_percentage') {
                    updateCategoryWeightsTotal(container, this.value);
                }
            });
        });

        document.getElementById('overall-ranking-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default submission first
            
            // Remove empty component rows before validation
            const rows = document.querySelectorAll('.component-row');
            console.log('Total rows before cleanup:', rows.length);
            
            rows.forEach(row => {
                const nameInput = row.querySelector('input[name*="[name]"]');
                const typeSelect = row.querySelector('select[name*="[type]"]');
                const percentageInput = row.querySelector('input[name*="[percentage]"]');
                
                // If all required fields are empty, remove the row
                const nameEmpty = !nameInput || !nameInput.value.trim();
                const typeEmpty = !typeSelect || !typeSelect.value;
                const percentageEmpty = !percentageInput || !percentageInput.value;
                
                console.log('Row check:', {
                    name: nameInput?.name,
                    nameValue: nameInput?.value,
                    nameEmpty, 
                    typeEmpty, 
                    percentageEmpty
                });
                
                if (nameEmpty && typeEmpty && percentageEmpty) {
                    console.log('Removing empty row');
                    row.remove();
                }
            });
            
            console.log('Total rows after cleanup:', document.querySelectorAll('.component-row').length);
            
            // Re-index remaining rows to ensure sequential indices
            const remainingRows = document.querySelectorAll('.component-row');
            remainingRows.forEach((row, newIndex) => {
                // Update all input names in this row
                row.querySelectorAll('input, select').forEach(input => {
                    if (input.name && input.name.includes('components[')) {
                        input.name = input.name.replace(/components\[\d+\]/, `components[${newIndex}]`);
                    }
                });
            });
            
            // Log all form data being submitted
            const formData = new FormData(this);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                if (key.includes('components')) {
                    console.log(key, '=', value);
                }
            }

            const total = parseFloat(document.getElementById('total-percentage').textContent);
            if (Math.abs(total - 100) > 0.01) {
                alert('Component percentages must sum to exactly 100%. Current total: ' + total.toFixed(2) + '%');
                return false;
            }

            // Validate category weights
            const filledRows = document.querySelectorAll('.component-row');
            for (let row of filledRows) {
                const typeSelect = row.querySelector('.component-type');
                if (typeSelect.value === 'category_percentage') {
                    const container = row.querySelector('.category-weights-container');
                    const weightItems = container.querySelectorAll('.category-weight-item');
                    
                    if (weightItems.length === 0) {
                        alert('Please add at least one category for each "Category Percentage" component.');
                        return false;
                    }

                    const componentPercentage = parseFloat(row.querySelector('.component-percentage').value) || 0;
                    const totalSpan = container.querySelector('.category-weights-total');
                    const weightsTotal = parseFloat(totalSpan.textContent) || 0;
                    
                    if (Math.abs(weightsTotal - componentPercentage) > 0.01) {
                        alert(`Category weights must sum to the component percentage (${componentPercentage}%). Current total: ${weightsTotal.toFixed(2)}%`);
                        return false;
                    }
                }
            }
            
            // All validations passed, submit the form
            this.submit();
        });

        // Add one initial row
        addComponentRow();

        updateTotalPercentage();
    });
</script>
@endif
@endsection
