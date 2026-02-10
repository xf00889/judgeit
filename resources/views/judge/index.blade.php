@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Assigned Categories</h1>
        <p class="mt-2 text-gray-600">Select a category to begin judging</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($categories->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-lg">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p>You are not currently assigned to any categories.</p>
            </div>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($categories as $category)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $category->name }}</h2>
                            @if($category->status === 'active')
                                <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                    Active
                                </span>
                            @elseif($category->status === 'locked')
                                <span class="px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                    Locked
                                </span>
                            @elseif($category->status === 'finalized')
                                <span class="px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                    Finalized
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                    Draft
                                </span>
                            @endif
                        </div>

                        @if($category->description)
                            <p class="text-gray-600 text-sm mb-4">{{ $category->description }}</p>
                        @endif

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">
                                <span class="font-medium">Criteria:</span> {{ $category->criteria->count() }}
                            </p>
                        </div>

                        @if($category->has_submitted)
                            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-3 py-2 rounded-lg text-sm mb-4">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Scores submitted
                                </div>
                            </div>
                        @endif

                        @if($category->isActive() && !$category->has_submitted)
                            <a href="{{ route('judge.categories.show', $category) }}" 
                               class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                                Start Judging
                            </a>
                        @elseif($category->isActive() && $category->has_submitted)
                            <button disabled 
                                    class="block w-full text-center bg-gray-100 text-gray-500 font-medium py-3 px-4 rounded-lg cursor-not-allowed">
                                Already Submitted
                            </button>
                        @else
                            <button disabled 
                                    class="block w-full text-center bg-gray-100 text-gray-500 font-medium py-3 px-4 rounded-lg cursor-not-allowed">
                                Not Available
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
