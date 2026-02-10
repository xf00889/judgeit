@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Criteria for {{ $category->name }}</h2>
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
            <div>
                @if($category->status->value === 'draft')
                    <a href="{{ route('admin.criteria.create', $category) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Criterion
                    </a>
                @endif
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Max Score
                    </th>
                    @if($category->status->value === 'draft')
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($criteria as $criterion)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $criterion->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $criterion->description ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $criterion->max_score }}</div>
                        </td>
                        @if($category->status->value === 'draft')
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.criteria.edit', [$category, $criterion]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Edit
                                </a>
                                <form action="{{ route('admin.criteria.destroy', [$category, $criterion]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this criterion?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $category->status->value === 'draft' ? '4' : '3' }}" class="px-6 py-4 text-center text-gray-500">
                            No criteria found for this category.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($category->status->value !== 'draft')
        <div class="px-6 py-4 bg-yellow-50 border-t border-yellow-200">
            <p class="text-sm text-yellow-800">
                <strong>Note:</strong> Criteria cannot be modified for categories that are active, locked, or finalized.
            </p>
        </div>
    @endif
</div>
@endsection
