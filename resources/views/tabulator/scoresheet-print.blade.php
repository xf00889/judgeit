<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judge->name }}'s Scoresheets - Print View</title>
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
        <h2 class="page-title">Judge Scoresheet Submissions</h2>
        <p style="text-align: center; margin-top: 0.5rem; font-size: 1.125rem; font-weight: 600;">{{ $judge->name }}</p>
    </div>

    @if($categoriesWithScores->isEmpty())
        <div style="text-align: center; padding: 2rem;">
            <p>No categories assigned to this judge.</p>
        </div>
    @else
        @foreach($categoriesWithScores as $item)
            <div class="category-section" style="margin-bottom: 2rem; page-break-inside: avoid;">
                <!-- Category Header -->
                <div style="background-color: #f3f4f6; padding: 0.75rem 1rem; border-bottom: 2px solid #e5e7eb; margin-bottom: 1rem;">
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700;">{{ $item['category']->name }}</h3>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: #6b7280;">
                        {{ $item['category']->criteria->count() }} criteria
                        @if($item['has_submitted'])
                            <span style="color: #059669; font-weight: 600;"> • Submitted</span>
                        @else
                            <span style="color: #d97706; font-weight: 600;"> • Not Submitted</span>
                        @endif
                    </p>
                </div>

                @if($item['has_submitted'])
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: left;">Contestant</th>
                                @foreach($item['category']->criteria as $criterion)
                                    <th style="text-align: center;">
                                        {{ $criterion->name }}<br>
                                        <span style="font-weight: normal; font-size: 0.875rem;">({{ $criterion->percentage }}%)</span>
                                    </th>
                                @endforeach
                                <th style="text-align: center; background-color: #dbeafe;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item['scores'] as $contestantId => $contestantScores)
                                @php
                                    $contestant = $contestantScores->first()->contestant;
                                    $totalScore = 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>#{{ $contestant->contestant_number }}</strong> {{ $contestant->name }}
                                    </td>
                                    @foreach($item['category']->criteria as $criterion)
                                        @php
                                            $score = $contestantScores->firstWhere('criterion_id', $criterion->id);
                                            if ($score) {
                                                $totalScore += $score->score_value;
                                            }
                                        @endphp
                                        <td style="text-align: center;">
                                            @if($score)
                                                {{ number_format($score->score_value, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="text-align: center; background-color: #dbeafe; font-weight: 700;">
                                        {{ number_format($totalScore, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #6b7280; padding: 2rem;">No scores submitted for this category</p>
                @endif
            </div>
        @endforeach
    @endif

    <script>
        window.onload = function() {
            // Small delay to ensure page is fully rendered
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
