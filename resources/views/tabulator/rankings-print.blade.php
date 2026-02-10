<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankings - Print View</title>
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
        <h2 class="page-title">Rankings</h2>
    </div>

    <div class="complete-rankings">
        @foreach($rankingsByGender as $gender => $rankings)
            <div class="gender-section">
                <h3>{{ $gender }}</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Number</th>
                            <th>Name</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankings as $ranking)
                            <tr class="rank-row rank-{{ $ranking->rank }}">
                                <td class="rank-cell">{{ $ranking->rank }}</td>
                                <td>{{ $ranking->contestant->contestant_number }}</td>
                                <td>{{ $ranking->contestant->name }}</td>
                                <td>{{ number_format($ranking->overallScore, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
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
