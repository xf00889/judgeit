@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Judge Assignments for {{ $category->name }}</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Status: 
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($category->status->value === 'draft') bg-gray-100 text-gray-800
                        @elseif($category->status->value === 'active') bg-green-100 text-green-800
                        @elseif($category->status->value === 'locked') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ ucfirst($category->status->value) }}
                    </span>
                </p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Categories
            </a>
        </div>
    </div>

    <div class="px-6 py-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Assigned Judges ({{ $assignedJudges->count() }})</h3>
        
        @if($assignedJudges->count() > 0)
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            @if(!$category->isFinalized())
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignedJudges as $judge)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $judge->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $judge->email }}</div>
                                </td>
                                @if(!$category->isFinalized())
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('admin.judges.destroy', [$category, $judge]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to remove this judge?')">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 mb-6">No judges assigned to this category yet.</p>
        @endif

        @if(!$category->isFinalized() && $availableJudges->count() > 0)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Assign Judge</h3>
                
                <form action="{{ route('admin.judges.store', $category) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Select Judge(s)
                        </label>
                        <div class="border rounded p-4 max-h-64 overflow-y-auto bg-gray-50">
                            @foreach($availableJudges as $judge)
                                <label class="flex items-center py-2 px-3 hover:bg-white rounded cursor-pointer transition-colors">
                                    <input type="checkbox" name="judge_ids[]" value="{{ $judge->id }}" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700">
                                        {{ $judge->name }} <span class="text-gray-500">({{ $judge->email }})</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('judge_ids')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                        @error('judge_ids.*')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Assign Selected Judges
                    </button>
                </form>
            </div>
        @elseif(!$category->isFinalized() && $availableJudges->count() === 0)
            <div class="border-t border-gray-200 pt-6">
                <p class="text-gray-500">All judges have been assigned to this category.</p>
            </div>
        @endif

        @if($category->isFinalized())
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded p-4">
                <p class="text-sm text-yellow-800">
                    <strong>Note:</strong> Judge assignments cannot be modified for finalized categories.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
