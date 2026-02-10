@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Assign Categories</h2>
                <p class="text-sm text-gray-600 mt-1">Judge: {{ $user->name }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Users
            </a>
        </div>
    </div>

    <form action="{{ route('admin.users.update-categories', $user) }}" method="POST" class="p-6">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Select Categories
            </label>
            <p class="text-sm text-gray-500 mb-4">
                Select one or more categories to assign to this judge. The judge will be able to score contestants in the selected categories.
            </p>

            @if($categories->isEmpty())
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
                    No categories available. Please create categories first.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($categories as $category)
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    type="checkbox" 
                                    name="categories[]" 
                                    value="{{ $category->id }}"
                                    id="category_{{ $category->id }}"
                                    {{ in_array($category->id, $assignedCategoryIds) ? 'checked' : '' }}
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="category_{{ $category->id }}" class="font-medium text-gray-700 cursor-pointer">
                                    {{ $category->name }}
                                </label>
                                @if($category->description)
                                    <p class="text-gray-500">{{ $category->description }}</p>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-1
                                    @if($category->status->value === 'active') bg-green-100 text-green-800
                                    @elseif($category->status->value === 'locked') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($category->status->value) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @error('categories')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                Cancel
            </a>
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline"
                {{ $categories->isEmpty() ? 'disabled' : '' }}
            >
                Save Assignments
            </button>
        </div>
    </form>
</div>

@if(session('success'))
    <x-toast type="success" message="{{ session('success') }}" />
@endif

@if(session('error'))
    <x-toast type="error" message="{{ session('error') }}" />
@endif
@endsection
