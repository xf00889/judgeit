@extends('layouts.app')

@section('content')
<x-card>
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Category Management</h2>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Category
            </a>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Criteria
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $category->order }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($category->status->value === 'draft') bg-gray-100 text-gray-800
                                @elseif($category->status->value === 'active') bg-green-100 text-green-800
                                @elseif($category->status->value === 'locked') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($category->status->value) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $category->criteria->count() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @if($category->status->value === 'draft')
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.categories.activate', $category) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            Activate
                                        </button>
                                    </form>
                                @elseif($category->status->value === 'active')
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.categories.deactivate', $category) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-900">
                                            Deactivate
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.criteria.index', $category) }}" class="text-purple-600 hover:text-purple-900">
                                    Criteria
                                </a>
                                <a href="{{ route('admin.judges.index', $category) }}" class="text-teal-600 hover:text-teal-900">
                                    Judges
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile/Tablet Card View -->
    <div class="lg:hidden divide-y divide-gray-200">
        @forelse($categories as $category)
            <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 text-xs font-medium text-gray-700">
                                {{ $category->order }}
                            </span>
                            <h3 class="text-base font-semibold text-gray-900">{{ $category->name }}</h3>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($category->status->value === 'draft') bg-gray-100 text-gray-800
                                @elseif($category->status->value === 'active') bg-green-100 text-green-800
                                @elseif($category->status->value === 'locked') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($category->status->value) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-medium bg-purple-50 text-purple-700 rounded-full">
                                {{ $category->criteria->count() }} Criteria
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($category->status->value === 'draft')
                        <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-md hover:bg-indigo-200 transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('admin.categories.activate', $category) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-sm font-medium rounded-md hover:bg-green-200 transition-colors">
                                Activate
                            </button>
                        </form>
                    @elseif($category->status->value === 'active')
                        <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-md hover:bg-indigo-200 transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('admin.categories.deactivate', $category) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors">
                                Deactivate
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.criteria.index', $category) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 text-sm font-medium rounded-md hover:bg-purple-200 transition-colors">
                        Criteria
                    </a>
                    <a href="{{ route('admin.judges.index', $category) }}" class="inline-flex items-center px-3 py-1.5 bg-teal-100 text-teal-700 text-sm font-medium rounded-md hover:bg-teal-200 transition-colors">
                        Judges
                    </a>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                No categories found.
            </div>
        @endforelse
    </div>
</x-card>
@endsection
