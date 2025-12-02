<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>List of Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;500&display=swap" rel="stylesheet">

    <style>
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

        /* Header */
        .header {
            position: relative;
            top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #2a67bc;
            padding-bottom: 10px;
            width: 500px;
            margin: 0 auto 20px auto;
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
            color: #000;
            font-size: 12px;
        }

        .header-right .span-2 {
            font-family: 'Poppins', sans-serif;
            font-size: 21px;
            font-weight: 300;
            color: #000;
        }

        .date {
            text-align: center;
            margin-top: 25px;
            font-weight: bold;
            font-size: 13px;
        }

        .stats {
            text-align: center;
            margin-top: 25px;
            font-weight: bold;
            font-size: 13px;
        }

        .title {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        /* Table */
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

        /* Footer */
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

    @php
        $department = auth()->user()->departments->first();
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

    @endphp

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/mandaue-logo.png') }}" alt="Mandaue Logo">
            <img src="{{ $departmentProfile ? $departmentLogo : $departmentLogo }}" alt="Department Logo">
        </div>

        <div class="header-right">
            <span class="span-1">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
            <span class="span-2">{{ strtoupper($departmentName) }}</span>
        </div>
    </div>


    <!-- DATE -->
    <div class="date">
        {{ now()->format('F d, Y') }}
    </div>

    <!-- TITLE -->
    <div class="title">
        LIST OF REPORTS
    </div>

    <div class="stats">
        Total Reports: {{ $grievances->count() }}
    </div>

    <!-- TABLE -->
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
        @forelse ($grievances as $index => $grievance)
            @php
                $rawRemarks = $grievance->grievance_remarks ?? [];
                $remarks = is_array($rawRemarks) ? $rawRemarks : json_decode($rawRemarks, true);

                $submittedBy = $grievance->is_anonymous
                    ? 'Anonymous'
                    : ($grievance->user
                        ? ($grievance->user->info
                            ? "{$grievance->user->info->first_name} {$grievance->user->info->last_name}"
                            : $grievance->user->name)
                        : '—');
            @endphp
            <tr>
                <td class="text-center text-bold">{{ $grievance->grievance_ticket_id }}</td>
                <td>{{ $grievance->grievance_title }}</td>
                <td class="text-center">{{ $grievance->grievance_type }}</td>
                <td class="text-center">{{ ucfirst($grievance->grievance_category) }}</td>
                <td class="text-center">{{ ucfirst($grievance->priority_level) }}</td>
                <td class="text-center">{{ ucwords(str_replace('_',' ', $grievance->grievance_status ?? '—')) }}</td>
                <td class="text-center">{{ $grievance->created_at->format('Y-m-d h:i A') }}</td>
                
                <td class="text-center">{{ $submittedBy }}</td>

                <td>{!! \Illuminate\Support\Str::limit(strip_tags($grievance->grievance_details), 120, '...') !!}</td>

                <td class="text-center">
                    @if ($grievance->attachments->count() > 0)
                        <strong>{{ $grievance->attachments->count() }} file(s)</strong>
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

    <!-- FOOTER -->
    <div class="status-footer">
        <div class="footer-row">
            <div class="footer-label">Noted by:</div>
            <div class="footer-value">
                @if(isset($hr_liaison))
                    {{ $hr_liaison->name }}
                @elseif(isset($admin))
                    {{ $admin->name }}
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="footer-subtext">
            @if(isset($hr_liaison))
                HR Liaison
            @elseif(isset($admin))
                Admin
            @else
                —
            @endif
        </div>
    </div>
</div>
</div>
</body>
</html>
