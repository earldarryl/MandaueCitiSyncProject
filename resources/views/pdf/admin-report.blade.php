<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $dynamicTitle ?? 'Admin Report' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400&display=swap" rel="stylesheet">

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

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #2a67bc;
            padding-bottom: 10px;
            width: 500px;
            margin: 20px auto 16px;
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

        .header-right .span-1 { font-size: 12px; color: #000; }
        .header-right .span-2 { font-size: 21px; font-weight: 300; color: #000; }

        .report-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .report-title {
            font-size: 26px;
            font-weight: 800;
            margin: 0;
            color: #1f2937;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .report-subtitle {
            margin-top: 6px;
            font-size: 16px;
            font-weight: 500;
            color: #4b5563;
        }

        .report-meta {
            margin-top: 10px;
            font-size: 13px;
            color: #6b7280;
        }

        .report-meta span {
            margin-right: 12px;
        }

        .summary-date {
            text-align:center;
            font-weight:600;
            margin-top:20px;
            margin-bottom:12px;
            font-size:13px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin: 25px 0;
        }

        .stat-card {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 18px 20px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .stat-card .stat-subtext {
            font-size: 13px;
            color: #6b7280;
        }

        .tag {
            display: inline-block;
            padding: 3px 8px;
            font-size: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            background-color: #f3f4f6;
            color: #374151;
        }

        .total-online {
            display: block;
            font-size: 1rem;
            line-height: 1.5;
            font-weight: 500;
            color: #6B7280;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #D1D5DB;
            padding: 4px 6px;
            word-wrap: break-word;
            vertical-align: middle;
        }

        th {
            font-size: 11px;
        }

        td {
            font-size: 10px;
        }

        td:nth-child(1), td:nth-child(2), td:nth-child(3) {
            text-align: left;
        }

        td span.status {
            display:inline-block;
            padding:2px 6px;
            border-radius:9999px;
            font-size:11px;
            font-weight:600;
        }

        table tr { page-break-inside: avoid; }

        .noted {
            display: flex;
            flex-direction: row;
            gap: 6px;
            margin-top: 50px;
            margin-left: 20px;
            font-size: 13px;
            width: fit-content;
        }

        .noted .name-with-role{
            display: flex;
            flex-direction: column;
            gap: 4px;
            align-content: center;
            align-items: center;
        }
        .noted .noted-text{
            display: flex;
            flex-direction: column;
            font-size:15px;
            align-content: center;
            align-items: center;
        }
        .noted .name {
            font-weight:600;
            font-size:15px;
            border-bottom:1.8px solid #374151;
            padding-bottom:2px;
            padding-left: 10px;
            padding-right: 10px;
        }
        .noted .position {
            text-align:center;
            font-size:12px;
            color:#6B7280;
            font-weight:500;
            margin-top:2px;
        }
    </style>

</head>
<body>

<div class="page">
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/mandaue-logo.png') }}" alt="Mandaue Logo">
        </div>
        <div class="header-right">
            <span class="span-1">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
            @if($filterType) <span class="span-2">{{ strtoupper($filterType) }}</span> @endif
        </div>
    </div>

    <div class="report-header">
        <div class="report-subtitle">
            {{ $dynamicTitle }}
        </div>
    </div>

    <div class="summary-date">
        @php
            $start = $startDate ? \Carbon\Carbon::parse($startDate) : \Carbon\Carbon::now();
            $end = $endDate ? \Carbon\Carbon::parse($endDate) : \Carbon\Carbon::now();
        @endphp

        {{ $start->format('F d, Y') }}
        @if($startDate !== $endDate)
            – {{ $end->format('F d, Y') }}
        @endif
    </div>

     @if(!empty($stats))
        <div class="stats-grid">
            @foreach($stats as $stat)
                <div class="stat-card">
                    <div class="tag" style="background: {{ $stat->bg ?? '#f3f4f6' }}; color: {{ $stat->text ?? '#374151' }}">
                        {{
                            $stat->label
                            ?? $stat->priority_level
                            ?? $stat->department_name
                            ?? $stat->grievance_type
                            ?? $stat->grievance_status
                            ?? 'N/A'
                        }}
                    </div>
                    <div class="stat-value">
                        {{ $stat->total ?? 0 }}
                    </div>

                    @if(isset($stat->total_online_time))
                        <div class="stat-subtext">
                            Total Online: {{ $stat->total_online_time }}
                        </div>
                    @endif
                    @if(isset($stat->percentage))
                        <div class="stat-subtext">
                            Percentage: {{ number_format($stat->percentage, 2) }}%
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <table style="width:100%; border-collapse:collapse; font-size:12px; font-family:Arial, sans-serif;">
        <thead style="background:#F3F4F6; color:#374151; text-transform:uppercase; font-weight:600; border-bottom:1px solid #D1D5DB;">
            <tr>
                @if($filterType === 'Grievances')
                    <th style="padding:8px; border:1px solid #E5E7EB;">TICKET ID</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">TITLE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">TYPE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">CATEGORY</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">DEPARTMENT</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">PRIORITY</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">STATUS</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">PROCESSING DAYS</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">DATE</th>

                @elseif($filterType === 'Departments')
                    <th style="padding:8px; border:1px solid #E5E7EB;">DEPARTMENT NAME</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">CODE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">TOTAL ASSIGNMENTS</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">HR LIAISONS ONLINE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">CREATED AT</th>

                @elseif($filterType === 'Feedbacks')
                    <th style="padding:8px; border:1px solid #E5E7EB;">EMAIL</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">SERVICE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">GENDER</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">REGION</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">CC SUMMARY</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">SQD SUMMARY</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">SUGGESTIONS</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">DATE</th>

                @elseif($filterType === 'Users' && $filterUserType === 'Citizen')
                    <th style="padding:8px; border:1px solid #E5E7EB;">FIRST NAME</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">MIDDLE NAME</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">LAST NAME</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">SUFFIX</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">GENDER</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">CIVIL STATUS</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">BARANGAY</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">SITIO</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">BIRTHDATE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">AGE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">PHONE</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">EMERGENCY NAME</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">EMERGENCY NUMBER</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">RELATIONSHIP</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">EMAIL</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">CREATED AT</th>

                @elseif($filterType === 'Users' && $filterUserType === 'HR Liaison')
                    <th style="padding:8px; border:1px solid #E5E7EB;">NAME</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">EMAIL</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">DEPARTMENT</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">STATUS</th>
                    <th style="padding:8px; border:1px solid #E5E7EB;">CREATED AT</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr style="background: {{ $index % 2 === 0 ? '#FFFFFF' : '#F9FAFB' }};">
                    @if($filterType === 'Grievances')
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->grievance_ticket_id }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item->grievance_title }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->grievance_type ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->grievance_category ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->departments->pluck('department_name')->join(', ') ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->priority_level ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->grievance_status ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->processing_days ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->created_at->format('Y-m-d h:i A') }}</td>

                    @elseif($filterType === 'Departments')
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->department_name }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->department_code }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->assignments_count ?? 0 }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->hrLiaisonsStatus ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->created_at->format('Y-m-d') }}</td>

                    @elseif($filterType === 'Feedbacks')
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item->email ?? 'N/A' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item->service }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->gender }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->region }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center; font-weight:bold;">{{ $item->cc_summary }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center; font-weight:bold;">{{ $item->sqd_summary }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item->suggestions }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">{{ $item->date->format('Y-m-d') }}</td>

                    @elseif($filterType === 'Users' && $filterUserType === 'Citizen')
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->first_name ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->middle_name ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->last_name ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->suffix ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->gender ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->civil_status ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->barangay ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->sitio ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional(optional($item['userInfo'])->birthdate)->format('Y-m-d') ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB; text-align:center;">
                            {{ optional($item['userInfo'])->age ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->phone_number ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->emergency_contact_name ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->emergency_contact_number ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['userInfo'])->emergency_relationship ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ $item['email'] ?? '—' }}
                        </td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">
                            {{ optional($item['created_at'])->format('Y-m-d') ?? '—' }}
                        </td>


                    @elseif($filterType === 'Users' && $filterUserType === 'HR Liaison')
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item['name'] }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item['email'] }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item['departments'] ?? '—' }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item['status'] }}</td>
                        <td style="padding:6px; border:1px solid #E5E7EB;">{{ $item['created_at']->format('Y-m-d') }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="16" style="text-align:center; font-style:italic; color:#6B7280; padding:12px;">
                        No data available for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>


    <div class="noted">
       <div class="noted-text">Noted:</div>
       <div class="name-with-role">
            <div class="name">{{ $adminName ?? 'N/A' }}</div>
            <div class="position">Admin</div>
       </div>
    </div>
</div>

</body>
</html>
