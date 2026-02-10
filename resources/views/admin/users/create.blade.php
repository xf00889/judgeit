@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Create New User</h2>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="px-6 py-4">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" 
                required>
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                Email <span class="text-red-500">*</span>
            </label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" 
                required>
            @error('email')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                Password <span class="text-red-500">*</span>
            </label>
            <input type="password" name="password" id="password" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" 
                required>
            @error('password')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                Confirm Password <span class="text-red-500">*</span>
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                required>
        </div>

        <div class="mb-6">
            <label for="role" class="block text-gray-700 text-sm font-bold mb-2">
                Role <span class="text-red-500">*</span>
            </label>
            <select name="role" id="role" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role') border-red-500 @enderror" 
                required>
                <option value="">Select a role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                        {{ ucfirst($role->value) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
