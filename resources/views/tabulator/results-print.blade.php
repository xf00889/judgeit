<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Results - Print View</title>
    <link rel="stylesheet" href="/css/print.css" media="all">
</head>
<body>
    <div class="print-header">
        <div class="header-with-logo">
            <img src="/brand.png" alt="School Logo" class="school-logo">
            <div class="vertical-separator"></div>
            <div class="header-text">
                <h1>Miguel L Daclan National High School - Senior High</h1>
                <p class="location">Culipapa, Hinoba-an, Negros Occidental</p>
            </div>
        </div>
        @if($activeRanking)
            <h2 class="page-title">{{ $activeRanking->name }}</h2>
            @if($activeRanking->description)
                <p class="subtitle">{{ $activeRanking->description }}</p>
            @endif
        @else
            <h2 class="page-title">Real-Time Results</h2>
        @endif
    </div>

    @if($activeRanking)
        @php
            $resultsByGender = collect($results)->groupBy(function($result) {
                return $result['contestant']->gender ?? 'Not Specified';
            });
        @endphp

        @foreach($resultsByGender as $gender => $genderResults)
            <div class="gender-section">
                <h3>{{ ucfirst($gender) }} Category</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Contestant #</th>
                            <th>Name</th>
                            @foreach($activeRanking->components as $component)
                                @if($component->type === 'fixed')
                                    <th>{{ $component->name }}</th>
                                @else
                                    @foreach($component->categoryWeights as $weight)
                                        <th>{{ $weight->category->name }}</th>
                                    @endforeach
                                @endif
                            @endforeach
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($genderResults as $result)
                            <tr class="rank-row rank-{{ $result['rank'] }}">
                                <td class="rank-cell">{{ $result['rank'] }}</td>
                                <td>{{ $result['contestant']->contestant_number }}</td>
                                <td>{{ $result['contestant']->name }}</td>
                                
                                @if(isset($result['breakdown']))
                                    @foreach($result['breakdown'] as $componentBreakdown)
                                        @if($componentBreakdown['type'] === 'fixed')
                                            <td>{{ number_format($componentBreakdown['component_score'] ?? 0, 2) }}</td>
                                        @else
                                            @foreach($componentBreakdown['categories'] as $category)
                                                <td>{{ number_format($category['weighted_score'] ?? 0, 2) }}</td>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                                
                                <td class="total-cell">{{ number_format($result['total_score'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div class="no-data">
            <p>No active overall ranking. Please activate a ranking to see results.</p>
        </div>
    @endif

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
