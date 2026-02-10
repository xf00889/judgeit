@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Category Winners</h1>
        <p class="text-gray-600 mt-2">Best in category winners by gender (displayed after 3 judges have scored)</p>
    </div>

    @if($categoryWinnersByGender && !empty($categoryWinnersByGender))
        @foreach($categoryWinnersByGender as $gender => $categories)
            <div class="mb-12">
                <!-- Gender Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 border-b-2 border-indigo-500 pb-2">
                        {{ $gender }} Category
                    </h2>
                </div>

                <!-- Category Winners Section for this Gender -->
                @if(!empty($categories))
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($categories as $categoryName => $winner)
                                <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-indigo-500 hover:shadow-xl transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $categoryName }}</h4>
                                        <span class="text-3xl">⭐</span>
                                    </div>
                                    <div class="bg-indigo-50 rounded-lg p-4 mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Best in Category Winner</p>
                                        <p class="text-2xl font-bold text-gray-900 mb-1">
                                            #{{ $winner['contestant']->contestant_number }}
                                        </p>
                                        <p class="text-lg font-semibold text-gray-800">
                                            {{ $winner['contestant']->name }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-xs text-gray-600 mb-1">Category Score</p>
                                        <p class="text-2xl font-bold text-indigo-600">
                                            {{ number_format($winner['score'], 2) }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Based on {{ \App\Models\Score::where('category_id', $winner['category_id'])->distinct('judge_id')->count() }} judge(s)
                                        </p>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('tabulator.categories.results', ['category' => $winner['category_id'], 'gender' => $gender]) }}" 
                                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            View Detailed Results →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <!-- No Category Winners Available -->
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No Category Winners Available</h3>
            <p class="mt-1 text-sm text-gray-500">
                Category winners will be displayed once at least 3 judges have submitted scores for each category.
            </p>
        </div>
    @endif

    <div class="mt-6 flex justify-center space-x-4">
        <button onclick="window.open('{{ route('tabulator.awards.print') }}', '_blank')" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Awards
        </button>
        <a href="{{ route('tabulator.results') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Results
        </a>
        <a href="{{ route('tabulator.rankings') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            View Rankings
        </a>
    </div>
</div>
@endsection
