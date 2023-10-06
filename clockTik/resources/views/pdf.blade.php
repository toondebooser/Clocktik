
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My PDF Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #333;
        }
    </style>
</head>
<body>
    <header>
        <h1>My PDF Document</h1>
    </header>
    <main>
        <p>This is the content of my PDF document.</p>
        <table>
            <thead>
                <tr>
                    <th>Column 1</th>
                    <th>Column 2</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{ $row['column1'] }}</td>
                    <td>{{ $row['column2'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
