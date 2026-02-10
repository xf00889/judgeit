<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalists - Print View</title>
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
        <h2 class="page-title">Finalists - Top 3 per Gender</h2>
    </div>

    <div class="finalists-list">
        @foreach($finalistsByGender as $gender => $finalists)
            <div class="gender-section">
                <h3>{{ $gender }} Category Finalists</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Number</th>
                            <th>Name</th>
                            <th>Preliminary Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($finalists as $finalist)
                            <tr class="rank-row rank-{{ $finalist->preliminary_rank }}">
                                <td class="rank-cell">{{ $finalist->preliminary_rank }}</td>
                                <td>{{ $finalist->contestant_number }}</td>
                                <td>{{ $finalist->name }}</td>
                                <td>{{ number_format($finalist->preliminary_score, 2) }}</td>
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
