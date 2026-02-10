@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Final Rankings</h1>
        <p class="text-gray-600 mt-2">Official pageant results and winners by gender</p>
    </div>

    @if($rankingsByGender->isNotEmpty())
        @foreach($rankingsByGender as $gender => $rankings)
            <div class="mb-12">
                <!-- Gender Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 border-b-2 border-blue-500 pb-2">
                        {{ $gender }} Category
                    </h2>
                </div>

                <!-- Top 3 Section for this Gender -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    @php
                        $first = $topThreeByGender[$gender]['winner'] ?? null;
                        $second = $topThreeByGender[$gender]['firstRunnerUp'] ?? null;
                        $third = $topThreeByGender[$gender]['secondRunnerUp'] ?? null;
                    @endphp

                    <!-- 1st Place -->
                    @if($first)
                        <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg shadow-xl p-6 text-center transform hover:scale-105 transition-transform">
                            <div class="text-6xl mb-4">🏆</div>
                            <h3 class="text-2xl font-bold text-yellow-900 mb-2">1st Place</h3>
                            <div class="bg-white bg-opacity-90 rounded-lg p-4">
                                <p class="text-3xl font-bold text-gray-900 mb-1">
                                    #{{ $first->contestant->contestant_number }}
                                </p>
                                <p class="text-xl font-semibold text-gray-800">
                                    {{ $first->contestant->name }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- 2nd Place -->
                    @if($second)
                        <div class="bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg shadow-xl p-6 text-center transform hover:scale-105 transition-transform">
                            <div class="text-6xl mb-4">🥈</div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">2nd Place</h3>
                            <div class="bg-white bg-opacity-90 rounded-lg p-4">
                                <p class="text-3xl font-bold text-gray-900 mb-1">
                                    #{{ $second->contestant->contestant_number }}
                                </p>
                                <p class="text-xl font-semibold text-gray-800">
                                    {{ $second->contestant->name }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- 3rd Place -->
                    @if($third)
                        <div class="bg-gradient-to-br from-orange-300 to-orange-400 rounded-lg shadow-xl p-6 text-center transform hover:scale-105 transition-transform">
                            <div class="text-6xl mb-4">🥉</div>
                            <h3 class="text-2xl font-bold text-orange-900 mb-2">3rd Place</h3>
                            <div class="bg-white bg-opacity-90 rounded-lg p-4">
                                <p class="text-3xl font-bold text-gray-900 mb-1">
                                    #{{ $third->contestant->contestant_number }}
                                </p>
                                <p class="text-xl font-semibold text-gray-800">
                                    {{ $third->contestant->name }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Complete Rankings Table for this Gender -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900">Complete {{ $gender }} Rankings</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rank
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contestant Number
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Overall Score
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category Breakdown
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rankings as $ranking)
                                    <tr class="hover:bg-gray-50 transition-colors
                                        {{ $ranking->rank === 1 ? 'bg-yellow-50' : '' }}
                                        {{ $ranking->rank === 2 ? 'bg-gray-100' : '' }}
                                        {{ $ranking->rank === 3 ? 'bg-orange-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($ranking->rank === 1)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-400 text-yellow-900">
                                                        🏆 {{ $ranking->rank }}
                                                    </span>
                                                @elseif($ranking->rank === 2)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-300 text-gray-900">
                                                        🥈 {{ $ranking->rank }}
                                                    </span>
                                                @elseif($ranking->rank === 3)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-orange-300 text-orange-900">
                                                        🥉 {{ $ranking->rank }}
                                                    </span>
                                                @else
                                                    <span class="text-lg font-semibold text-gray-700">
                                                        {{ $ranking->rank }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ $ranking->contestant->contestant_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $ranking->contestant->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-lg font-bold text-blue-900">
                                                {{ number_format($ranking->overallScore, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($ranking->categoryScores as $categoryName => $score)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $categoryName }}: {{ number_format($score, 2) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tie Information for this Gender -->
                @php
                    $tiedRanks = $rankings->groupBy('rank')->filter(fn($group) => $group->count() > 1);
                @endphp
                
                @if($tiedRanks->isNotEmpty())
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">
                                    Tied Rankings in {{ $gender }} Category
                                </h4>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>The following contestants have tied scores and share the same rank:</p>
                                    <ul class="list-disc list-inside mt-2">
                                        @foreach($tiedRanks as $rank => $contestants)
                                            <li>
                                                <strong>Rank {{ $rank }}:</strong> 
                                                {{ $contestants->pluck('contestant.name')->join(', ') }}
                                                (Score: {{ number_format($contestants->first()->overallScore, 2) }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <!-- No Rankings Available -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No Rankings Available</h3>
            <p class="mt-1 text-sm text-gray-500">
                Rankings will be generated once all categories are finalized and scores are submitted.
            </p>
        </div>
    @endif

    <div class="mt-6 flex justify-center space-x-4">
        <button onclick="window.open('{{ route('tabulator.rankings.print') }}', '_blank')" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Rankings
        </button>
        <a href="{{ route('tabulator.results') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Results
        </a>
        <a href="{{ route('tabulator.awards') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            View Awards
        </a>
    </div>
</div>
@endsection
