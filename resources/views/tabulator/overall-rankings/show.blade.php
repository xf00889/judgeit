@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $overallRanking->name }} - Results</h2>
            @if($overallRanking->description)
                <p class="text-sm text-gray-600 mt-1">{{ $overallRanking->description }}</p>
            @endif
            <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full {{ $overallRanking->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $overallRanking->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <a href="{{ route('tabulator.overall-rankings.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Rankings
        </a>
    </div>

    <!-- Formula Breakdown -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Formula Breakdown</h3>
        <div class="space-y-2">
            @foreach($overallRanking->components as $component)
                <div class="bg-white p-3 rounded border border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <span class="font-semibold text-gray-800">{{ $component->name }}</span>
                            <span class="text-sm text-gray-600 ml-2">({{ $component->percentage }}%)</span>
                            
                            @if($component->type === 'fixed' && $component->category)
                                <div class="text-sm text-gray-600 mt-1">
                                    Category: {{ $component->category->name }}
                                </div>
                            @elseif($component->type === 'category_percentage')
                                <div class="text-sm text-gray-600 mt-1">
                                    Categories:
                                    @foreach($component->categoryWeights as $weight)
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs mr-1 mt-1">
                                            {{ $weight->category->name }} ({{ $weight->percentage }}%)
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Results Table -->
    <div class="px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Contestant Rankings</h3>
        
        @if($results->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                <p class="text-yellow-800 text-sm">No scores available yet. Contestants will appear here once judges submit their scores.</p>
            </div>
        @else
            @foreach($rankedResultsByGender as $gender => $genderResults)
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-purple-700 mb-3 bg-purple-50 px-4 py-2 rounded">{{ ucfirst($gender) }} Category</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-bold">Rank</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-bold">Contestant #</th>
                                    <th class="border border-gray-300 px-4 py-3 text-left text-sm font-bold">Name</th>
                                    <th class="border border-gray-300 px-4 py-3 text-right text-sm font-bold">Overall Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($genderResults as $result)
                                    <tr class="hover:bg-gray-50 {{ $result['rank'] === 1 ? 'bg-yellow-50' : '' }}">
                                        <td class="border border-gray-300 px-4 py-3">
                                            <span class="font-bold text-lg {{ $result['rank'] === 1 ? 'text-yellow-600' : 'text-gray-700' }}">
                                                #{{ $result['rank'] }}{{ $result['is_tie'] ?? false ? ' (Tie)' : '' }}
                                                @if($result['rank'] === 1)
                                                    <span class="ml-2">👑</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-gray-700">
                                            {{ $result['contestant']->contestant_number }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 font-semibold text-gray-800">
                                            {{ $result['contestant']->name }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-right">
                                            <span class="text-lg font-bold {{ $result['rank'] === 1 ? 'text-yellow-600' : 'text-blue-600' }}">
                                                {{ number_format($result['total_score'], 2) }}
                                            </span>
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

<script>
    // Auto-refresh every 10 seconds to show updated scores
    setInterval(function() {
        location.reload();
    }, 10000);
</script>
@endsection
