<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Results & Tally</title>
    <link rel="stylesheet" href="/css/print.css" media="all">
    <style>
        /* Additional styles for category results */
        .criteria-header {
            font-size: 0.75rem;
            text-align: center;
        }
        
        .judge-scores {
            font-size: 0.7rem;
            color: #666;
            line-height: 1.2;
        }
        
        .criterion-average {
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .total-cell {
            background-color: #e0e7ff !important;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        table td {
            vertical-align: top;
        }
        
        .judges-panel {
            margin-top: 1rem;
            padding: 0.5rem;
            background-color: #f9fafb;
            border: 1px solid #ddd;
        }
        
        .judges-panel h4 {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .judge-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            margin: 0.125rem;
            background-color: #dbeafe;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
        
        @media print {
            .criteria-header {
                font-size: 7pt;
            }
            
            .judge-scores {
                font-size: 6pt;
            }
            
            .criterion-average {
                font-size: 8pt;
            }
            
            .total-cell {
                background-color: #e0e7ff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .judges-panel {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .judge-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
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
        <h2 class="page-title">{{ $category->name }} - Complete Results & Tally</h2>
        @if($genderFilter)
            <p class="subtitle">{{ ucfirst($genderFilter) }} Category</p>
        @endif
    </div>

    <!-- Judges Panel -->
    <div class="judges-panel">
        <h4>Judges Panel:</h4>
        @foreach($category->judges as $judge)
            <span class="judge-badge">{{ $judge->name }}</span>
        @endforeach
    </div>

    <!-- Results Table -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 60px;">Rank</th>
                <th rowspan="2" style="width: 80px;">Number</th>
                <th rowspan="2">Contestant Name</th>
                @foreach($category->criteria as $criterion)
                    <th class="criteria-header">
                        {{ $criterion->name }}
                        <br>
                        <span style="font-size: 0.7rem;">(Max: {{ $criterion->percentage }}%)</span>
                    </th>
                @endforeach
                <th rowspan="2" style="width: 80px;">Total Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr class="rank-row rank-{{ $result['rank'] }}">
                    <td class="rank-cell">{{ $result['rank'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $result['contestant']->contestant_number }}</td>
                    <td style="font-weight: 600;">{{ $result['contestant']->name }}</td>
                    
                    @foreach($category->criteria as $criterion)
                        @php
                            $breakdown = $result['criterion_breakdown'][$criterion->id] ?? null;
                            $average = $breakdown ? $breakdown['average'] : 0;
                            $scores = $breakdown ? $breakdown['scores'] : collect();
                        @endphp
                        <td style="text-align: center;">
                            <div class="criterion-average">{{ number_format($average, 2) }}</div>
                            @if($scores->isNotEmpty())
                                <div class="judge-scores">
                                    @foreach($scores as $score)
                                        <div>{{ $score->judge->name }}: {{ number_format($score->score_value, 2) }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    @endforeach
                    
                    <td class="total-cell" style="text-align: center;">
                        {{ number_format($result['total_score'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Legend -->
    <div style="margin-top: 1rem; padding: 0.5rem; border: 1px solid #ddd; background-color: #f9fafb;">
        <h4 style="font-size: 0.9rem; margin-bottom: 0.5rem;">Legend:</h4>
        <div style="font-size: 0.8rem;">
            <p><strong>Bold numbers</strong> = Average score from all judges</p>
            <p><strong>Small text below</strong> = Individual judge scores for transparency</p>
            <p><strong>Total Score</strong> = Sum of all criterion averages</p>
        </div>
    </div>

    <!-- Criteria Details -->
    <div style="margin-top: 1rem; page-break-before: avoid;">
        <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">Judging Criteria Details:</h3>
        <table style="margin-top: 0.5rem;">
            <thead>
                <tr>
                    <th>Criterion</th>
                    <th style="width: 100px;">Maximum Score</th>
                    <th style="width: 100px;">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category->criteria as $criterion)
                    <tr>
                        <td>{{ $criterion->name }}</td>
                        <td style="text-align: center;">{{ $criterion->max_score }}</td>
                        <td style="text-align: center;">{{ $criterion->percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
