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
            padding: 8px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 8px;
        }

        .box h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .images img {
            height: 10px;
            width: 10px;
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

        .header {
            font-weight: bold;
            font-size: 16px;
        }

        .label {
            font-weight: bold;
        }

        .icon {
            color: #C8E6C9;
            margin-bottom: -10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Safety Inspection Report</h2>
        <p><span class="label">Site Name:</span> {{ $inspection->site->name }}</p>
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
            <h3 class="header">Element: {{ $category['category_name'] }} ({{ $category['count'] }}
                issue{{ $category['count'] > 1 ? 's' : '' }})</h3>

            @foreach ($category['issues'] as $issue)
                <div class="box">
                    <h4>{{ $issue['category_detail_name'] }}
                        <span class="label">Condition:</span>
                        @if ($issue['condition'] === null && count($issue['files']) <= 0)
                            <span class="icon">
                                <svg fill="#000000" height="24px" width="24px" version="1.1" id="Capa_1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    viewBox="0 0 490.4 490.4" xml:space="preserve">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                    </g>
                                    <g id="SVGRepo_iconCarrier">
                                        <g>
                                            <g>
                                                <path
                                                    d="M245.2,0C110,0,0,110,0,245.2s110,245.2,245.2,245.2s245.2-110,245.2-245.2S380.4,0,245.2,0z M245.2,465.9 c-121.7,0-220.7-99-220.7-220.7s99-220.7,220.7-220.7s220.7,99,220.7,220.7S366.9,465.9,245.2,465.9z">
                                                </path>
                                                <path
                                                    d="M309.4,185.5l-94,93.5l-34.3-34.5c-4.8-4.8-12.5-4.8-17.3-0.1c-4.8,4.7-4.8,12.5-0.1,17.3l42.9,43.2 c2.4,2.4,5.5,3.6,8.7,3.6c3.1,0,6.2-1.2,8.6-3.6l102.7-102.1c4.8-4.8,4.8-12.5,0.1-17.3C321.9,180.7,314.2,180.7,309.4,185.5z">
                                                </path>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </span>
                        @else
                            @if ($issue['condition'] !== null)
                                <span class="label">{{ $issue['condition'] }} </span>
                            @else
                                <span class="label">Not Specified</span>
                            @endif
                        @endif

                    </h4>
                    @if (count($issue['files']) > 0)
                        <div class="images">
                            @foreach ($issue['files'] as $file)
                                <img src="{{ public_path($file['path']) }}" alt="{{ $file['file_name'] }}"
                                    width="100" height="100" />
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    {{-- 
@foreach ($inspectionData as $category)
    <div class="box">
        <h4>{{ $category['category_name'] }}</h4>
        <ul>
            @foreach ($category['issues'] as $issue)
                <li>
                    <strong>{{ $issue['category_detail_name'] }}:</strong>
                    @if ($issue['condition'])
                        {{ $issue['condition'] }}
                        @if (!empty($issue['files']))
                            <div class="images">
                                @foreach ($issue['files'] as $file)
                                    <img src="{{ public_path('storage/' . $file) }}" alt="Issue Image" style="width: 50px; height: auto;">
                                @endforeach
                            </div>
                        @endif
                    @else
                        ✅ Checked – No issues found
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endforeach --}}

</body>

</html>
