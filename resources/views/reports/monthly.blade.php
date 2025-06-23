<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>{{ $title }}</h1>
    <p>Date: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
