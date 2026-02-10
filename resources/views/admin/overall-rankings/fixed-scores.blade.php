@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Manage Fixed Scores</h1>
            <p class="text-gray-600 mt-2">{{ $overallRanking->name }}</p>
        </div>
        <a href="{{ route('admin.overall-rankings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Rankings
        </a>
    </div>
</div>

@if($fixedComponents->isEmpty())
    <div class="bg-white shadow-md rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No fixed components</h3>
        <p class="mt-1 text-sm text-gray-500">This overall ranking doesn't have any fixed type components.</p>
    </div>
@else
    <form action="{{ route('admin.overall-rankings.fixed-scores.save', $overallRanking) }}" method="POST">
        @csrf
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">
                                Contestant
                            </th>
                            @foreach($fixedComponents as $component)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $component->name }}
                                    <span class="text-gray-400">({{ $component->percentage }}% - Max: {{ $component->percentage }})</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contestants as $contestant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                                    <div class="text-sm font-medium text-gray-900">
                                        #{{ $contestant->contestant_number }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $contestant->name }}
                                    </div>
                                </td>
                                @foreach($fixedComponents as $component)
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <input 
                                                type="number" 
                                                name="scores[{{ $contestant->id }}][{{ $component->id }}]" 
                                                step="0.01" 
                                                min="0" 
                                                max="{{ $component->percentage }}"
                                                value="{{ old('scores.' . $contestant->id . '.' . $component->id, $existingScores[$contestant->id][$component->id]->score ?? '') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                placeholder="0.00"
                                            >
                                            <textarea 
                                                name="notes[{{ $contestant->id }}][{{ $component->id }}]" 
                                                rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                                                placeholder="Notes (optional)"
                                            >{{ old('notes.' . $contestant->id . '.' . $component->id, $existingScores[$contestant->id][$component->id]->notes ?? '') }}</textarea>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.overall-rankings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded transition-colors">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded transition-colors">
                Save Fixed Scores
            </button>
        </div>
    </form>
@endif
@endsection
