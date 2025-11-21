<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 0;
            background-repeat: no-repeat;
            background-position: center top;
            background-size: cover;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #1F2937;
        }

        .page {
            width: 794px;
            min-height: 1122px;
            padding: 40px;
            box-sizing: border-box;
            page-break-after: always;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #2a67bc;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
            text-align: center;
        }

        .header-right .span-1 {
            font-size: 12px;
            color: #000;
        }

        .header-right .span-2 {
            font-size: 20px;
            font-weight: 500;
            color: #000;
        }

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

        .table-container {
            width: 100%;
            overflow-x: auto;
            background-color: rgba(255, 255, 255, 0.85);
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            padding: 12px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            border: 1px solid #D1D5DB;
            padding: 8px;
            background-color: #F3F4F6;
            color: #374151;
            text-transform: uppercase;
            font-weight: 600;
            text-align: center;
        }

        td {
            border: 1px solid #D1D5DB;
            padding: 6px;
            font-size: 12px;
        }

        td.center {
            text-align: center;
        }

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
            </div>
        </div>

        <div class="report-header">
            <div class="report-subtitle">
                {{ $dynamicTitle }}
            </div>
        </div>

        <div class="report-title">
            @if($selectedDate)
                {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
            @else
                <div></div>
            @endif
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="center">ID</th>
                        <th>Module</th>
                        <th>Action Type</th>
                        <th>Action</th>
                        <th class="center">Executed At</th>
                        <th>Platform</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="center">{{ $log->activiy_log_id }}</td>
                            <td>{{ $log->module ?? 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $log->action_type)) }}</td>
                            <td>{{ str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $log->action))) }}</td>
                            <td class="center">{{ \Carbon\Carbon::parse($log->timestamp ?? $log->created_at)->format('M d, Y h:i A') }}</td>
                            <td>{{ $log->platform ?? 'N/A' }}</td>
                            <td>{{ $log->location ?? 'Unknown' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; font-style:italic; color:#6B7280;">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="noted">
            <div class="noted-text">Noted:</div>
            <div class="name-with-role">
                <div class="name">{{ $user->name ?? 'N/A' }}</div>
                <div class="position">
                    {{ $user->getRoleNames()->first()
                        ? ucwords(str_replace('_', ' ', $user->getRoleNames()->first()))
                        : 'N/A'
                    }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
