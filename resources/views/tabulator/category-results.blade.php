@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">{{ $category->name }} - Detailed Results</h1>
                <p class="text-gray-600 mt-2">
                    Complete tally and breakdown for transparency
                    @if($genderFilter)
                        <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $genderFilter }} Category Only
                        </span>
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.open('{{ route('tabulator.categories.results.print', ['category' => $category->id, 'gender' => $genderFilter]) }}', '_blank')" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Results
                </button>
                <a href="{{ route('tabulator.awards') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Awards
                </a>
            </div>
        </div>
    </div>

    <!-- Category Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <p class="text-lg font-semibold text-gray-900 capitalize">{{ $category->status->value }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Assigned Judges</p>
                <p class="text-lg font-semibold text-gray-900">{{ $category->judges->count() }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Criteria</p>
                <p class="text-lg font-semibold text-gray-900">{{ $category->criteria->count() }}</p>
            </div>
        </div>
        
        @if($category->description)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">Description</p>
                <p class="text-gray-900">{{ $category->description }}</p>
            </div>
        @endif
    </div>

    <!-- Criteria Breakdown -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Judging Criteria</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($category->criteria as $criterion)
                <div class="bg-indigo-50 rounded-lg p-4">
                    <p class="font-semibold text-gray-900">{{ $criterion->name }}</p>
                    <p class="text-sm text-gray-600">Max Score: {{ $criterion->max_score }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">Complete Results & Tally</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rank
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contestant
                        </th>
                        @foreach($category->criteria as $criterion)
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $criterion->name }}
                                <br>
                                <span class="text-xs text-gray-400">({{ $criterion->percentage }}%)</span>
                            </th>
                        @endforeach
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-50">
                            Total Score
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($results as $result)
                        <tr class="hover:bg-gray-50 transition-colors
                            {{ $result['rank'] === 1 ? 'bg-yellow-50' : '' }}
                            {{ $result['rank'] === 2 ? 'bg-gray-100' : '' }}
                            {{ $result['rank'] === 3 ? 'bg-orange-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($result['is_tie'])
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-900">
                                        🤝 T-{{ $result['rank'] }}
                                    </span>
                                @elseif($result['rank'] === 1)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-400 text-yellow-900">
                                        🏆 {{ $result['rank'] }}
                                    </span>
                                @elseif($result['rank'] === 2)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-300 text-gray-900">
                                        🥈 {{ $result['rank'] }}
                                    </span>
                                @elseif($result['rank'] === 3)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-orange-300 text-orange-900">
                                        🥉 {{ $result['rank'] }}
                                    </span>
                                @else
                                    <span class="text-lg font-semibold text-gray-700">
                                        {{ $result['rank'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    #{{ $result['contestant']->contestant_number }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $result['contestant']->name }}
                                </div>
                            </td>
                            @foreach($category->criteria as $criterion)
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $breakdown = $result['criterion_breakdown'][$criterion->id] ?? null;
                                        $average = $breakdown ? $breakdown['average'] : 0;
                                        $scores = $breakdown ? $breakdown['scores'] : collect();
                                    @endphp
                                    
                                    <div class="text-xl font-bold text-indigo-600 mb-2">
                                        {{ number_format($average, 2) }}
                                    </div>
                                    
                                    @if($scores->isNotEmpty())
                                        <div class="space-y-1 mt-2 pt-2 border-t border-gray-200">
                                            @foreach($scores as $score)
                                                <div class="flex items-center justify-between text-sm bg-gray-50 px-2 py-1 rounded">
                                                    <span class="text-gray-700 font-medium">{{ $score->judge->name }}:</span>
                                                    <span class="text-gray-900 font-semibold">{{ number_format($score->score_value, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400 italic mt-2">No scores</div>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-6 py-4 text-center bg-indigo-50">
                                <div class="text-2xl font-bold text-indigo-600">
                                    {{ number_format($result['total_score'], 2) }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Judges Information -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Judges Panel</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($category->judges as $judge)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $judge->name }}
                </span>
            @endforeach
        </div>
    </div>

    <!-- Navigation -->
    <div class="mt-8 flex justify-center">
        <a href="{{ route('tabulator.awards') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Awards
        </a>
    </div>
</div>
@endsection
