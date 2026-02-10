@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Create New Category</h2>
    </div>

    <form action="{{ route('tabulator.categories.store') }}" method="POST" class="px-6 py-4" id="category-form">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                Category Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" 
                required>
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                Description
            </label>
            <textarea name="description" id="description" rows="3" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="order" class="block text-gray-700 text-sm font-bold mb-2">
                Order
            </label>
            <input type="number" name="order" id="order" value="{{ old('order', 0) }}" 
                min="0"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('order') border-red-500 @enderror">
            <p class="text-gray-500 text-xs italic mt-1">Lower numbers appear first</p>
            @error('order')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Judging Criteria <span class="text-red-500">*</span>
            </label>
            <p class="text-gray-500 text-xs italic mb-3">Add criteria for judges to score. Percentages must sum to 100%.</p>
            
            <div class="overflow-x-auto mb-4">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold">Criterion Name</th>
                            <th class="border border-gray-300 px-4 py-2 text-left text-sm font-bold">Percentage (%)</th>
                            <th class="border border-gray-300 px-4 py-2 text-center text-sm font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="criteria-table-body">
                        <tr class="criteria-row">
                            <td class="border border-gray-300 px-4 py-2">
                                <input type="text" name="criteria[0][name]" 
                                    placeholder="e.g., Poise, Grace, Talent"
                                    class="w-full px-2 py-1 border rounded"
                                    required>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <input type="number" name="criteria[0][percentage]" 
                                    step="0.01" min="0" max="100"
                                    placeholder="0.00"
                                    class="w-full px-2 py-1 border rounded criterion-percentage"
                                    required>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm remove-criterion-btn">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                <p class="text-sm text-blue-800">
                    Total Percentage: <span id="total-percentage" class="font-bold">0</span>%
                    <span id="percentage-status" class="ml-2"></span>
                </p>
            </div>

            <button type="button" id="add-criterion-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                + Add Criterion
            </button>

            @error('criteria')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Category
            </button>
            <a href="{{ route('tabulator.results') }}" class="text-gray-600 hover:text-gray-800">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = document.querySelectorAll('.criteria-row').length;

        function updateTotalPercentage() {
            const percentageInputs = document.querySelectorAll('.criterion-percentage');
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

        function addCriterionRow() {
            const tbody = document.getElementById('criteria-table-body');
            const newRow = document.createElement('tr');
            newRow.className = 'criteria-row';
            newRow.innerHTML = `
                <td class="border border-gray-300 px-4 py-2">
                    <input type="text" name="criteria[${rowCount}][name]" 
                        placeholder="e.g., Poise, Grace, Talent"
                        class="w-full px-2 py-1 border rounded"
                        required>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    <input type="number" name="criteria[${rowCount}][percentage]" 
                        step="0.01" min="0" max="100"
                        placeholder="0.00"
                        class="w-full px-2 py-1 border rounded criterion-percentage"
                        required>
                </td>
                <td class="border border-gray-300 px-4 py-2 text-center">
                    <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm remove-criterion-btn">
                        Remove
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            rowCount++;

            const percentageInput = newRow.querySelector('.criterion-percentage');
            percentageInput.addEventListener('input', updateTotalPercentage);

            const removeBtn = newRow.querySelector('.remove-criterion-btn');
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                newRow.remove();
                updateTotalPercentage();
            });

            updateTotalPercentage();
        }

        document.getElementById('add-criterion-btn').addEventListener('click', function(e) {
            e.preventDefault();
            addCriterionRow();
        });

        document.querySelectorAll('.remove-criterion-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('tr').remove();
                updateTotalPercentage();
            });
        });

        document.querySelectorAll('.criterion-percentage').forEach(input => {
            input.addEventListener('input', updateTotalPercentage);
        });

        document.getElementById('category-form').addEventListener('submit', function(e) {
            const total = parseFloat(document.getElementById('total-percentage').textContent);
            if (total !== 100) {
                e.preventDefault();
                alert('Criteria percentages must sum to exactly 100%. Current total: ' + total.toFixed(2) + '%');
                return false;
            }
        });

        updateTotalPercentage();
    });
</script>
@endsection
