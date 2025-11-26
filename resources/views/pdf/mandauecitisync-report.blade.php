<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grievance Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500&display=swap" rel="stylesheet">

    @php
        $bgImagePath = public_path('images/grievance-report-template-bg.jpg');
        $bgImageBase64 = '';
        if(file_exists($bgImagePath)) {
            $ext = pathinfo($bgImagePath, PATHINFO_EXTENSION);
            $bgImageBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($bgImagePath));
        }
    @endphp

    <style>
        @page {
            size: A4;
            margin: 20px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            color: #1F2937;
            background-color: #fff;
        }

        .page {
            width: 794px;
            margin: 0 auto;
            position: relative;
            padding: 20px;
            page-break-after: always;
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
                        $departmentLogo = Storage::url($departmentProfile);
                    } else {
                        $departmentLogo = 'https://ui-avatars.com/api/?name=' . urlencode($departmentName) . '&background=' . $bgColor . '&color=fff&size=128';
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
                <img src="{{ $departmentLogo }}" alt="Department Logo">
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


            <div style="margin:20px; border:1px solid #D1D5DB; background-color: rgba(255,255,255,0.85); overflow-x:auto; border-radius:0.5rem; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                <table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:13px;">
                    <thead style="background-color:#F3F4F6; color:#374151; text-transform:uppercase; font-weight:600;">
                        <tr>
                            <th style="border:1px solid #D1D5DB; padding:8px; text-align:center;">TICKET ID</th>
                            <th style="border:1px solid #D1D5DB; padding:8px;">TITLE</th>
                            <th style="border:1px solid #D1D5DB; padding:8px; text-align:center;">TYPE</th>
                            <th style="border:1px solid #D1D5DB; padding:8px; text-align:center;">CATEGORY</th>
                            <th style="border:1px solid #D1D5DB; padding:8px; text-align:center;">STATUS</th>
                            <th style="border:1px solid #D1D5DB; padding:8px; text-align:center;">PRIORITY LEVEL</th>
                            <th style="border:1px solid #D1D5DB; padding:8px; text-align:center;">PROCESSING DAYS</th>
                            <th style="border:1px solid #D1D5DB; padding:8px; text-align:center;">DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr style="transition: background-color 0.2s; cursor:default;">
                                <td style="border:1px solid #D1D5DB; padding:6px; text-align:center;">{{ $item->grievance_ticket_id }}</td>
                                <td style="border:1px solid #D1D5DB; padding:6px;">{{ $item->grievance_title }}</td>
                                <td style="border:1px solid #D1D5DB; padding:6px; text-align:center; text-transform:capitalize;">{{ $item->grievance_type ?? '—' }}</td>
                                <td style="border:1px solid #D1D5DB; padding:6px; text-align:center; text-transform:capitalize;">{{ $item->grievance_category ?? '—' }}</td>
                                <td style="border:1px solid #D1D5DB; padding:6px; text-align:center;">
                                    <span style="display:inline-block; padding:2px 6px; border-radius:9999px; font-size:11px; font-weight:600;">
                                        {{ strtoupper($item->grievance_status) }}
                                    </span>
                                </td>
                                <td style="border:1px solid #D1D5DB; padding:6px; text-align:center;">
                                    <span style="display:inline-block; padding:2px 6px; border-radius:9999px; font-size:11px; font-weight:600;">
                                        {{ strtoupper($item->priority_level) }}
                                    </span>
                                </td>
                                <td style="border:1px solid #D1D5DB; padding:6px; text-align:center;">{{ $item->processing_days ?? '—' }}</td>
                                <td style="border:1px solid #D1D5DB; padding:6px; text-align:center;">{{ $item->created_at->format('Y-m-d h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding:10px; font-style:italic; color:#6B7280;">No data available for the selected dates.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
