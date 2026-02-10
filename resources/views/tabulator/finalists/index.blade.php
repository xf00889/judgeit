@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Finalist Selection</h1>
        <p class="mt-2 text-gray-600">Select the top 3 contestants to advance to the finals round.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if(!$can_select && $selection_reason)
        <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <strong>Cannot Finalize:</strong> {{ $selection_reason }}
        </div>
    @endif

    @if($can_select && $selection_reason)
        <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
            <strong>Ready:</strong> {{ $selection_reason }}
        </div>
    @endif

    <!-- Finalize Action -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Finalize Top 3 per Gender
        </h2>
        <p class="text-gray-600 mb-4">
            This will automatically select the top 3 contestants for each gender based on their overall scores. 
            Contestants not in the top 3 of their gender will be marked as inactive.
        </p>
        
        <div class="flex gap-4">
            <form action="{{ route('tabulator.finalists.finalize') }}" method="POST" 
                  onsubmit="return confirm('This will finalize the top 3 contestants per gender and mark others as inactive. Are you sure you want to proceed?');">
                @csrf
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-semibold
                        {{ !$can_select ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !$can_select ? 'disabled' : '' }}>
                    Finalize Top 3 per Gender
                </button>
            </form>

            @if($has_finalists)
                <button onclick="window.open('{{ route('tabulator.finalists.print') }}', '_blank')" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Finalists
                </button>

                <form action="{{ route('tabulator.finalists.reset') }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to reset finalist selection? This will clear all finalist data.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded font-semibold">
                        Reset Selection
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Preliminary Rankings Preview -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Preliminary Rankings by Gender</h2>
        
        @if($rankings->isEmpty())
            <p class="text-gray-600">No rankings available yet. Contestants need to be scored first.</p>
        @else
            @foreach($rankingsByGender as $gender => $genderRankings)
                <div class="mb-8">
                    <!-- Gender Header -->
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-800 border-b-2 border-blue-500 pb-2">
                            {{ $gender }} Category
                        </h3>
                    </div>

                    <!-- Top 3 for this Gender -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        @php
                            $first = $topThreeByGender[$gender]['first'] ?? null;
                            $second = $topThreeByGender[$gender]['second'] ?? null;
                            $third = $topThreeByGender[$gender]['third'] ?? null;
                        @endphp

                        <!-- 1st Place -->
                        @if($first)
                            <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg shadow-lg p-4 text-center">
                                <div class="text-4xl mb-2">🏆</div>
                                <h4 class="text-lg font-bold text-yellow-900 mb-2">1st Place</h4>
                                <div class="bg-white bg-opacity-90 rounded-lg p-3">
                                    <p class="text-2xl font-bold text-gray-900 mb-1">
                                        #{{ $first->contestant->contestant_number }}
                                    </p>
                                    <p class="text-base font-semibold text-gray-800">
                                        {{ $first->contestant->name }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Score: {{ number_format($first->overallScore, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- 2nd Place -->
                        @if($second)
                            <div class="bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg shadow-lg p-4 text-center">
                                <div class="text-4xl mb-2">🥈</div>
                                <h4 class="text-lg font-bold text-gray-900 mb-2">2nd Place</h4>
                                <div class="bg-white bg-opacity-90 rounded-lg p-3">
                                    <p class="text-2xl font-bold text-gray-900 mb-1">
                                        #{{ $second->contestant->contestant_number }}
                                    </p>
                                    <p class="text-base font-semibold text-gray-800">
                                        {{ $second->contestant->name }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Score: {{ number_format($second->overallScore, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- 3rd Place -->
                        @if($third)
                            <div class="bg-gradient-to-br from-orange-300 to-orange-400 rounded-lg shadow-lg p-4 text-center">
                                <div class="text-4xl mb-2">🥉</div>
                                <h4 class="text-lg font-bold text-orange-900 mb-2">3rd Place</h4>
                                <div class="bg-white bg-opacity-90 rounded-lg p-3">
                                    <p class="text-2xl font-bold text-gray-900 mb-1">
                                        #{{ $third->contestant->contestant_number }}
                                    </p>
                                    <p class="text-base font-semibold text-gray-800">
                                        {{ $third->contestant->name }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Score: {{ number_format($third->overallScore, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Complete Rankings Table for this Gender -->
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Overall Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($genderRankings as $ranking)
                                    <tr class="hover:bg-gray-50 {{ $ranking->rank <= 3 ? 'bg-blue-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                @if($ranking->rank == 1) bg-yellow-100 text-yellow-800
                                                @elseif($ranking->rank == 2) bg-gray-100 text-gray-800
                                                @elseif($ranking->rank == 3) bg-orange-100 text-orange-800
                                                @else bg-white text-gray-800
                                                @endif">
                                                #{{ $ranking->rank }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $ranking->contestant->contestant_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $ranking->contestant->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($ranking->overallScore, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ranking->rank <= 3)
                                                <span class="text-blue-600 font-semibold">Will Advance</span>
                                            @else
                                                <span class="text-gray-500">Eliminated</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
