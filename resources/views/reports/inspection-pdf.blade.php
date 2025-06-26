<!DOCTYPE html>
<html>

<head>
    <title>Inspection Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .section {
            margin-bottom: 30px;
        }

        .summary-boxes div {
            display: inline-block;
            width: 24%;
            padding: 10px;
            margin-right: 1%;
            text-align: center;
            border-radius: 6px;
        }

        .good {
            background-color: #C8E6C9;
            width: 25%;
        }

        .worn {
            background-color: #EBA53566;
            width: 25%;
        }

        .failed {
            background-color: #FFC7C7;
            width: 25%;
        }

        .total {
            background-color: #3E3E4B;
            color: #fff;
            width: 25%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .group-title {
            font-weight: bold;
            background: #f0f0f0;
            padding: 8px;
            margin-top: 20px;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h2>PPE Inspection Report</h2>
    <p><strong>Site Name:</strong> {{ $inspection->site->name }}</p>
    <p><span class="label">Inspection #:</span> {{ $inspection->id }}</p>
    <p><strong>Inspector:</strong> {{ $inspection->assigned_to->name ?? 'N/A' }}</p>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($inspection->complete_date)->toDateString() }}</p>

    {{-- <div class="section summary-boxes">
        <table>
            <thead>
                <tr>
                    <th class="total">Total Checked</th>
                    <th class="good">Good</th>
                    <th class="worn">Worn but OK</th>
                    <th class="failed">Failed and Removed</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="total">{{ $inspection_details->count() }}</td>
                    <td class="good">{{ $inspection_details->where('condition', 0)->count() }}</td>
                    <td class="worn">{{ $inspection_details->where('condition', 1)->count() }}</td>
                    <td class="failed">{{ $inspection_details->where('condition', 2)->count() }}</td>

                </tr>
            </tbody>
        </table>
    </div> --}}



    @php
        $grouped = $inspection_details->groupBy('category_item.name');
    @endphp

    @php
        // Group by category name
        $grouped = $inspection_details->groupBy('category_item.name')->map(function ($items) {
            return [
                'items' => $items,
                'total' => $items->count(),
                'good' => $items->where('condition', 0)->count(),
                'worn' => $items->where('condition', 1)->count(),
                'failed' => $items->where('condition', 2)->count(),
            ];
        });
    @endphp


    @foreach ($grouped as $categoryName => $data)
        <div class="group-title">
            {{ $categoryName }}
            (Total: {{ $data['total'] }},
            Good: {{ $data['good'] }},
            Worn: {{ $data['worn'] }},
            Failed: {{ $data['failed'] }})
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Last Checked</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['items'] as $item)
                    <tr>
                        <td>{{ $item->category_detail->name }}</td>
                        <td>
                            @if ($item->condition === 0)
                                Good
                            @elseif($item->condition === 1)
                                Worn but OK
                            @elseif($item->condition === 2)
                                Failed and Removed
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->toDateString() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>

</html>
