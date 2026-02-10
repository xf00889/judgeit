<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Winners - Print View</title>
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
        <h2 class="page-title">Category Winners</h2>
    </div>

    <div class="category-winners">
        @foreach($categoryWinnersByGender as $gender => $categories)
            <div class="gender-section">
                <h3>{{ $gender }} Category Winners</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Number</th>
                            <th>Name</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $categoryName => $winner)
                            <tr>
                                <td>{{ $categoryName }}</td>
                                <td>{{ $winner['contestant']->contestant_number }}</td>
                                <td>{{ $winner['contestant']->name }}</td>
                                <td>{{ number_format($winner['score'], 2) }}</td>
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
