@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Overall Rankings</h1>
    <a href="{{ route('admin.overall-rankings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create Overall Ranking
    </a>
</div>

@if($rankings->isEmpty())
    <div class="bg-white shadow-md rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No overall rankings</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new overall ranking formula.</p>
        <div class="mt-6">
            <a href="{{ route('admin.overall-rankings.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Overall Ranking
            </a>
        </div>
    </div>
@else
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Components</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($rankings as $ranking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $ranking->name }}</div>
                            @if($ranking->description)
                                <div class="text-sm text-gray-500">{{ $ranking->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @foreach($ranking->components as $component)
                                    <div class="mb-2">
                                        <span class="font-medium">{{ $component->name }}</span>: 
                                        <span class="text-gray-600">{{ $component->percentage }}%</span>
                                        <span class="text-xs text-gray-500">({{ ucfirst(str_replace('_', ' ', $component->type)) }})</span>
                                        
                                        @if($component->type === 'category_percentage' && $component->categoryWeights->isNotEmpty())
                                            <div class="ml-4 mt-1 text-xs text-gray-600">
                                                @foreach($component->categoryWeights as $weight)
                                                    <div>• {{ $weight->category->name }}: {{ $weight->percentage }}%</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($ranking->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.overall-rankings.results', $ranking) }}" class="text-purple-600 hover:text-purple-900">View Results</a>
                            @if($ranking->components->where('type', 'fixed')->isNotEmpty())
                                <a href="{{ route('admin.overall-rankings.fixed-scores', $ranking) }}" class="text-orange-600 hover:text-orange-900">Fixed Scores</a>
                            @endif
                            <a href="{{ route('admin.overall-rankings.edit', $ranking) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            
                            @if($ranking->is_active)
                                <form action="{{ route('admin.overall-rankings.deactivate', $ranking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900">Deactivate</button>
                                </form>
                            @else
                                <form action="{{ route('admin.overall-rankings.activate', $ranking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900">Activate</button>
                                </form>
                            @endif
                            
                            @if(!$ranking->is_active)
                                <form action="{{ route('admin.overall-rankings.destroy', $ranking) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this overall ranking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
