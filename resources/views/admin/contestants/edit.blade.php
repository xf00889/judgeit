@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Edit Contestant</h2>
    </div>

    <form action="{{ route('admin.contestants.update', $contestant) }}" method="POST" class="px-6 py-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="contestant_number" class="block text-gray-700 text-sm font-bold mb-2">
                Contestant Number <span class="text-red-500">*</span>
            </label>
            <input type="text" name="contestant_number" id="contestant_number" value="{{ old('contestant_number', $contestant->contestant_number) }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('contestant_number') border-red-500 @enderror" 
                required>
            @error('contestant_number')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name', $contestant->name) }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" 
                required>
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">
                Gender
            </label>
            <select name="gender" id="gender" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('gender') border-red-500 @enderror">
                <option value="">Select Gender</option>
                <option value="male" {{ strtolower(old('gender', $contestant->gender ?? '')) == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ strtolower(old('gender', $contestant->gender ?? '')) == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ strtolower(old('gender', $contestant->gender ?? '')) == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('gender')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="profile_details" class="block text-gray-700 text-sm font-bold mb-2">
                Profile Details
            </label>
            <textarea name="profile_details" id="profile_details" rows="4" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('profile_details') border-red-500 @enderror">{{ old('profile_details', $contestant->profile_details) }}</textarea>
            @error('profile_details')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $contestant->is_active) ? 'checked' : '' }} 
                    class="form-checkbox h-5 w-5 text-blue-600">
                <span class="ml-2 text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Contestant
            </button>
            <a href="{{ route('admin.contestants.index') }}" class="text-gray-600 hover:text-gray-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
