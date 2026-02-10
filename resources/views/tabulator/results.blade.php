@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Loading Indicator -->
    <div id="loading-indicator" class="hidden fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 bg-blue-500 text-white rounded-lg shadow-lg">
        <div class="flex items-center">
            <svg class="animate-spin h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Updating results...
        </div>
    </div>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Real-Time Results</h1>
            <p class="text-gray-600 mt-2">Live scoring results and judging progress</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tabulator.results.print') }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Results
            </a>
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Results
            </button>
        </div>
    </div>

    <!-- Judging Progress Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 judging-progress-section">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Judging Progress</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($progress as $categoryProgress)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $categoryProgress['category_name'] }}</h3>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">
                            {{ $categoryProgress['submitted_count'] }} / {{ $categoryProgress['total_judges'] }} judges
                        </span>
                        <span class="text-sm font-semibold text-blue-600">
                            {{ $categoryProgress['percentage'] }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $categoryProgress['percentage'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($activeRanking)
        <!-- Overall Ranking Info -->
        <div class="bg-blue-600 rounded-xl shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">{{ $activeRanking->name }}</h2>
                    @if($activeRanking->description)
                        <p class="text-blue-100 mt-2">{{ $activeRanking->description }}</p>
                    @endif
                </div>
                <a href="{{ route('tabulator.overall-rankings.show', $activeRanking) }}" 
                   class="inline-flex items-center px-4 py-2 bg-white text-blue-600 hover:bg-blue-50 font-medium rounded-lg transition-colors">
                    View Breakdown →
                </a>
            </div>
        </div>

        @php
            // Group results by gender
            $resultsByGender = collect($results)->groupBy(function($result) {
                return $result['contestant']->gender ?? 'Not Specified';
            });
        @endphp

        @foreach($resultsByGender as $gender => $genderResults)
            <!-- Gender Category Table -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="bg-purple-600 px-6 py-4">
                    <h3 class="text-2xl font-bold text-white">{{ ucfirst($gender) }} Category</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <!-- First row: Component headers -->
                            <tr class="bg-gray-200">
                                <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase" rowspan="2">
                                    Contestant No.
                                </th>
                                
                                @foreach($activeRanking->components as $component)
                                    @if($component->type === 'fixed')
                                        <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase" rowspan="2">
                                            {{ $component->name }}
                                            <br>
                                            <span class="text-xs font-normal text-gray-500">({{ $component->percentage }}%)</span>
                                        </th>
                                    @else
                                        <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase bg-purple-100" colspan="{{ $component->categoryWeights->count() }}">
                                            {{ $component->name }}
                                            <br>
                                            <span class="text-xs font-normal text-gray-500">({{ $component->percentage }}% Total)</span>
                                        </th>
                                    @endif
                                @endforeach

                                <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-white uppercase bg-blue-600" rowspan="2">
                                    Total
                                </th>
                                <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-white uppercase bg-green-600" rowspan="2">
                                    Rank
                                </th>
                            </tr>
                            
                            <!-- Second row: Category sub-headers -->
                            <tr class="bg-gray-100">
                                @foreach($activeRanking->components as $component)
                                    @if($component->type !== 'fixed')
                                        @foreach($component->categoryWeights as $weight)
                                            <th class="border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase">
                                                {{ $weight->category->name }}
                                                <br>
                                                <span class="text-xs font-normal text-gray-500">({{ $weight->percentage }}%)</span>
                                            </th>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($genderResults as $result)
                                <tr class="hover:bg-gray-50 transition-colors
                                    {{ $result['rank'] === 1 ? 'bg-yellow-50' : '' }}
                                    {{ $result['rank'] === 2 ? 'bg-gray-100' : '' }}
                                    {{ $result['rank'] === 3 ? 'bg-orange-50' : '' }}">
                                    
                                    <!-- Contestant Number -->
                                    <td class="border-2 border-gray-300 px-4 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-2xl font-bold text-gray-900">#{{ $result['contestant']->contestant_number }}</span>
                                            <span class="text-sm text-gray-600 mt-1">{{ $result['contestant']->name }}</span>
                                        </div>
                                    </td>

                                    <!-- Component Scores -->
                                    @if(isset($result['breakdown']))
                                        @foreach($result['breakdown'] as $componentBreakdown)
                                            @if($componentBreakdown['type'] === 'fixed')
                                                <td class="border-2 border-gray-300 px-4 py-4 text-center">
                                                    <span class="text-lg font-semibold text-gray-900">
                                                        {{ number_format($componentBreakdown['component_score'] ?? 0, 2) }}
                                                    </span>
                                                </td>
                                            @else
                                                @foreach($componentBreakdown['categories'] as $category)
                                                    <td class="border-2 border-gray-300 px-4 py-4 text-center">
                                                        <span class="text-lg font-semibold text-gray-900">
                                                            {{ number_format($category['weighted_score'] ?? 0, 2) }}
                                                        </span>
                                                    </td>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif

                                    <!-- Total Score -->
                                    <td class="border-2 border-gray-300 px-4 py-4 text-center bg-blue-50">
                                        <span class="text-2xl font-bold text-blue-900">
                                            {{ number_format($result['total_score'], 2) }}
                                        </span>
                                    </td>

                                    <!-- Rank -->
                                    <td class="border-2 border-gray-300 px-4 py-4 text-center bg-green-50">
                                        @if($result['rank'] === 1)
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold bg-yellow-400 text-yellow-900">
                                                🏆 {{ $result['rank'] }}
                                            </span>
                                        @elseif($result['rank'] === 2)
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold bg-gray-300 text-gray-900">
                                                🥈 {{ $result['rank'] }}
                                            </span>
                                        @elseif($result['rank'] === 3)
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold bg-orange-300 text-orange-900">
                                                🥉 {{ $result['rank'] }}
                                            </span>
                                        @else
                                            <span class="text-2xl font-bold text-gray-700">
                                                {{ $result['rank'] }}
                                            </span>
                                        @endif
                                        @if($result['is_tie'] ?? false)
                                            <div class="text-xs text-purple-600 font-semibold mt-1">TIE</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 2 + count($activeRanking->components) + 2 }}" class="border-2 border-gray-300 px-6 py-8 text-center text-gray-500">
                                        No contestants in this category yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    @else
        <!-- No Active Ranking Warning -->
        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-bold text-yellow-800">
                        No Active Overall Ranking
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Please activate an overall ranking to see calculated scores.</p>
                        <a href="{{ route('tabulator.overall-rankings.index') }}" class="underline font-semibold hover:text-yellow-900 mt-2 inline-block">
                            Manage Overall Rankings →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6 flex justify-between items-center">
        <div class="flex space-x-3">
            <a href="{{ route('tabulator.overall-rankings.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Manage Rankings
            </a>
            
            <a href="{{ route('tabulator.rankings') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                View Final Rankings
            </a>
        </div>
    </div>
</div>

@push('scripts')
@vite(['resources/js/realtime-results.js'])
@endpush
@endsection
