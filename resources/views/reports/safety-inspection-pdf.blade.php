<!DOCTYPE html>
<html>

<head>
    <title>Safety Inspection Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 25px;
        }

        .box {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .box h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .images img {
            height: 100px;
            width: auto;
            margin: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Safety Inspection Report</h2>
        <p><span class="label">Inspection #:</span> {{ $inspection->id }}</p>
        <p><span class="label">Inspector:</span> {{ $inspection->assigned_to->name ?? 'N/A' }}</p>
        <p><span class="label">Completed:</span> {{ \Carbon\Carbon::parse($inspection->complete_date)->toDateString() }}
        </p>
        <p><span class="label">Issues Reported:</span> {{ $issueCount }}</p>
    </div>

    <div class="section">
        <h3>General Comments</h3>
        <p>{{ $inspection->closing_comment ?? 'No comments' }}</p>
    </div>

    @foreach ($groupedData as $category)
        <div class="section">
            <h3>Element: {{ $category['category_name'] }} ({{ $category['count'] }}
                issue{{ $category['count'] > 1 ? 's' : '' }})</h3>

            @foreach ($category['issues'] as $issue)
                @if ($issue['condition'] !== null || count($issue['files']) > 0)
                    <div class="box">
                        <h4>Reference: {{ $issue['category_detail_name'] }}</h4>
                        <p><span class="label">Condition:</span>
                            @switch($issue['condition'])
                                @case(0)
                                    Good
                                @break

                                @case(1)
                                    Worn but OK
                                @break

                                @case(2)
                                    Failed and Removed
                                @break

                                @default
                                    Not specified
                            @endswitch
                        </p>

                        @if (count($issue['files']) > 0)
                            <div class="images">
                                @foreach ($issue['files'] as $file)
                                    <img src="{{ public_path($file['path']) }}" alt="{{ $file['file_name'] }}">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach

</body>

</html>
