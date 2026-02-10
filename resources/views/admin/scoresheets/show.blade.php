@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.scoresheets.index') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-all duration-200 shadow-sm hover:shadow">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Judges List
        </a>
    </div>

    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-3xl font-bold">{{ $judge->name }}'s Scoresheets</h1>
                <p class="text-blue-100 mt-2">All category submissions</p>
            </div>
            <div class="bg-blue-800 bg-opacity-30 rounded-lg px-6 py-4 border border-blue-400 border-opacity-30">
                <div class="text-blue-100 text-sm font-medium">Total Categories</div>
                <div class="text-white text-3xl font-bold">{{ $categoriesWithScores->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    @if($categoriesWithScores->isNotEmpty())
        <div class="flex justify-end mb-6">
            <a href="{{ route('admin.scoresheets.print', $judge->id) }}" 
               target="_blank"
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </a>
        </div>
    @endif

    @if($categoriesWithScores->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Categories Assigned</h3>
            <p class="text-gray-600">This judge has not been assigned to any categories yet.</p>
        </div>
    @else
        @foreach($categoriesWithScores as $item)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                <!-- Category Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $item['category']->name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ $item['category']->criteria->count() }} criteria</p>
                        </div>
                        <div>
                            @if($item['has_submitted'])
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Submitted
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    Not Submitted
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Scores Table or Empty State -->
                @if($item['has_submitted'])
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                                        Contestant
                                    </th>
                                    @foreach($item['category']->criteria as $criterion)
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                            <div>{{ $criterion->name }}</div>
                                            <div class="text-xs font-normal text-gray-500 normal-case mt-1">{{ $criterion->percentage }}%</div>
                                        </th>
                                    @endforeach
                                    <th class="px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider bg-blue-50 border-l-2 border-blue-200">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($item['scores'] as $contestantId => $contestantScores)
                                    @php
                                        $contestant = $contestantScores->first()->contestant;
                                        $totalScore = 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white z-10 border-r border-gray-200">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-600 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold">{{ $contestant->contestant_number }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900">{{ $contestant->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($item['category']->criteria as $criterion)
                                            @php
                                                $score = $contestantScores->firstWhere('criterion_id', $criterion->id);
                                                if ($score) {
                                                    $totalScore += $score->score_value;
                                                }
                                            @endphp
                                            <td class="px-6 py-4 text-center">
                                                @if($score)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                                        {{ number_format($score->score_value, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-6 py-4 text-center bg-blue-50 border-l-2 border-blue-200">
                                            <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-bold bg-blue-600 text-white">
                                                {{ number_format($totalScore, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 text-sm">No scores submitted for this category yet</p>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
