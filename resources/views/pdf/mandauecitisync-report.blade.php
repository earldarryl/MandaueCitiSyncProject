<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grievance Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4;
        }

       body {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 0;
            margin: 0;
            opacity: 1 !important;
            animation: none !important;
            font-family: 'Poppins', sans-serif;
        }

        .page {
            width: 100%;
        }

        .content {
            position: relative;
            z-index: 10;
        }


        .header {
            position: relative;
            top: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #2a67bc;
            padding-bottom: 10px;
            width: 500px;
            box-sizing: border-box;
            margin: 0 auto 16px auto;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 30%;
            justify-content: center;
            border-right: 2px solid #1F2937;
            padding-right: 12px;
        }

        .header-left img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #fff;
        }

        .header-right {
            width: 70%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            line-height: 1.4;
        }

        .header-right .span-1 {
            color: #000000;
            font-size: 12px;
            text-align: center;
        }

        .header-right .span-2 {
            font-family: 'Poppins', sans-serif;
            font-size: 21px;
            font-weight: 300;
            color: #000000;
            text-align: center;
        }

        .report-header {
            position: relative;
            text-align: center;
            margin-top: 60px;
            border-bottom: 2px solid #e5e7eb;
        }

        .report-subtitle {
            margin-top: 6px;
            font-size: 16px;
            font-weight: 500;
            color: #4b5563;
        }

        .summary-date {
            position: relative;
            text-align:center;
            font-weight:600;
            margin-top:45px;
            margin-bottom:12px;
            font-family:sans-serif;
            font-size:13px;
        }

        .total-reports-stat {
            text-align:center;
            font-weight:600;
            margin-bottom:12px;
            font-family:sans-serif;
            font-size:13px;
        }

        .status-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin: 20px 0;
        }

        .status-card {
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, box-shadow 0.2s;
            font-family: sans-serif;
            margin: 5px;
        }

        .status-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .status-content {
            text-align: center;
        }

        .status-count {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .status-label {
            font-size: 1rem;
            margin-top: 4px;
        }

        .summary-cards {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
        }

        .summary-card {
            padding: 6px 12px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
            background: white !important;
            font-size: 11px !important;
            color: #1f2937 !important;
        }

        .grievance-remark-td {
            width: 30% !important;
            max-width: 30% !important;
            white-space: normal !important;
        }

        table thead {
            background: #f3f4f6 !important;
            text-transform: uppercase !important;
            font-weight: bold !important;
            color: #374151 !important;
        }

        table th {
            border: 1px solid #d1d5db !important;
            padding: 6px !important;
        }

        table td {
            border: 1px solid #d1d5db !important;
            padding: 6px !important;
            word-wrap: break-word !important;
        }

        table tbody tr:nth-child(even) {
            background: #f9fafb !important;
        }

        table tbody tr:nth-child(odd) {
            background: #ffffff !important;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .status-footer {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 50px;
            margin-left: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            width: fit-content;
            color: #1f2937;
        }

        .status-footer .footer-row {
            display: flex;
            align-items: flex-end;
            gap: 6px;
        }

        .status-footer .footer-label {
            font-weight: 600;
            color: #374151;
        }

        .status-footer .footer-value {
            font-weight: 600;
            font-size: 15px;
            border-bottom: 1.8px solid #374151;
            padding-bottom: 2px;
            letter-spacing: 0.3px;
            color: #111827;
        }

        .status-footer .footer-subtext {
            text-align: center;
            font-size: 12px;
            color: #6B7280;
            font-weight: 500;
            margin-top: 2px;
            letter-spacing: 0.2px;
        }

    </style>
</head>
<body>
<div class="page">
    <div class="content">
        <div class="header">
            <div class="header-left">
                @php
                    $total = array_sum($statuses);
                    $department = $user->departments->first();
                    $departmentName = $department->department_name ?? 'N/A';
                    $departmentProfile = $department->department_profile ?? null;

                    $palette = ['0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899','14B8A6','6366F1','F97316','84CC16'];
                    $index = crc32($departmentName) % count($palette);
                    $bgColor = $palette[$index];

                    if ($departmentProfile) {
                        $departmentLogoPath = storage_path('app/public/' . $departmentProfile);
                    } else {
                        $departmentLogoPath = 'https://ui-avatars.com/api/?name=' . urlencode($departmentName) . '&background=' . $bgColor . '&color=fff&size=128';
                    }

                    function formatNumber($number) {
                        if ($number >= 1000000) {
                            return round($number / 1000000, 1) . 'M';
                        } elseif ($number >= 1000) {
                            return round($number / 1000, 1) . 'K';
                        }
                        return $number;
                    }

                @endphp

                <img src="{{ public_path('images/mandaue-logo.png') }}" alt="Mandaue Logo">
                <img src="{{ $departmentLogoPath }}" alt="Department Logo">
            </div>

            <div class="header-right">
                <span class="span-1">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
                <span class="span-2">{{ strtoupper($departmentName) }}</span>
            </div>
        </div>

        <div class="report-header">
            <div class="report-subtitle">
                {{ $dynamicTitle }}
            </div>
        </div>

        <div class="summary-date">
            {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}
            @if($startDate !== $endDate) – {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }} @endif
        </div>

        <div class="total-reports-stat">
            Total Reports: {{ $data->count() }}
        </div>

        <div class="status-cards">
            @foreach($statuses as $label => $count)
                @php
                    $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;

                    switch ($label) {
                        case 'Pending':
                            $bgColor = '#FEF3C7';
                            $textColor = '#B45309';
                            break;
                        case 'Overdue':
                            $bgColor = '#FEE2E2';
                            $textColor = '#991B1B';
                            break;
                        case 'Resolved':
                            $bgColor = '#DCFCE7';
                            $textColor = '#166534';
                            break;
                        default:
                            $bgColor = '#E5E7EB';
                            $textColor = '#374151';
                    }
                @endphp

                <div class="status-card" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                    <div class="status-content">
                        <div class="status-count"> {{ formatNumber($count) }} ({{ $percentage }}%)</div>
                        <div class="status-label">{{ $label }}</div>
                    </div>
                </div>
            @endforeach
        </div>

            <table>
                <thead>
                    <tr>
                        <th>TICKET ID</th>
                        <th>TITLE</th>
                        <th>TYPE</th>
                        <th>CATEGORY</th>
                        <th>PRIORITY</th>
                        <th>STATUS</th>
                        <th>DATE FILED</th>
                        <th>SUBMITTED BY</th>
                        <th>DETAILS</th>
                        <th>ATTACHMENTS</th>
                        <th>REMARKS</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($data as $item)
                    @php
                        $rawRemarks = $item->grievance_remarks ?? [];
                        $remarks = is_array($rawRemarks) ? $rawRemarks : json_decode($rawRemarks, true);

                        $submittedBy = $item->is_anonymous
                            ? 'Anonymous'
                            : ($item->user
                                ? ($item->user->info
                                    ? "{$item->user->info->first_name} {$item->user->info->last_name}"
                                    : $item->user->name)
                                : '—');
                    @endphp

                    <tr>
                        <td class="text-center text-bold">{{ $item->grievance_ticket_id }}</td>
                        <td>{{ $item->grievance_title }}</td>
                        <td class="text-center">{{ $item->grievance_type }}</td>
                        <td class="text-center">{{ ucfirst($item->grievance_category) }}</td>
                        <td class="text-center">{{ ucfirst($item->priority_level) }}</td>
                        <td class="text-center">{{ ucwords(str_replace('_',' ', $item->grievance_status ?? '—')) }}</td>
                        <td class="text-center">{{ $item->created_at->format('Y-m-d h:i A') }}</td>

                        <td class="text-center">{{ $submittedBy }}</td>

                        <td>{!! \Illuminate\Support\Str::limit(strip_tags($item->grievance_details), 120, '...') !!}</td>

                        <td class="text-center">
                            @if ($item->attachments->count() > 0)
                                <strong>{{ $item->attachments->count() }} file(s)</strong>
                            @else
                                <span style="color:#666;">None</span>
                            @endif
                        </td>

                        <td class="grievance-remark-td">
                            @if (!empty($remarks))
                                @foreach ($remarks as $remark)
                                    <div>
                                        <strong>[{{ date('Y-m-d H:i', strtotime($remark['timestamp'])) }}]</strong>
                                        {{ $submittedBy }}
                                        ({{ $remark['role'] ?? '—' }}):
                                        {{ $remark['message'] ?? '' }}
                                    </div>
                                @endforeach
                            @else
                                <span style="color:#666;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center" style="font-style: italic; color: #777;">
                            No reports available.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="status-footer">
                <div class="footer-row">
                    <div class="footer-label">Noted by:</div>
                    <div class="footer-value">{{ $hrName ?? 'N/A' }}</div>
                </div>
                <div class="footer-subtext">HR Liaison</div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
    Chart.register(ChartDataLabels);

    const statusCounts = @json($statuses ?? ['Pending'=>0,'Overdue'=>0,'Resolved'=>0]);
    const labels = Object.keys(statusCounts);
    const chartData = Object.values(statusCounts);

    const backgroundColors = [
        'rgba(255, 193, 7, 0.6)',
        'rgba(220, 38, 38, 0.6)',
        'rgba(34, 197, 94, 0.6)'
    ];

    const borderColors = [
        'rgba(0, 0, 0, 1)',
        'rgba(0, 0, 0, 1)',
        'rgba(0, 0, 0, 1)'
    ];

    new Chart(document.getElementById('grievanceChart').getContext('2d'), {
        type: 'pie',
        data: { labels, datasets:[{ data: chartData, backgroundColor: backgroundColors, borderColor: borderColors, borderWidth: 1 }] },
        options: {
            responsive: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 12 } } },
                datalabels: {
                    color: '#000',
                    font: { weight:'bold', size:12 },
                    formatter: (value, context) => {
                        if(value===0) return '';
                        const total = context.chart.data.datasets[0].data.reduce((a,b)=>a+b,0);
                        const percentage = ((value/total)*100).toFixed(1);
                        return `${context.chart.data.labels[context.dataIndex]}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>
</html>
