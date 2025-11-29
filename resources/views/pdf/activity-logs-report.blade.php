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
            padding: 20px;
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
            align-items: end;
            gap: 12px;
            width: 30%;
            justify-content: end;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
            padding: 0 10px;
        }

        .stats-card {
            position: relative;
            background: linear-gradient(to bottom right, #f0f5ff, #dbeafe);
            border: 1px solid #bfdbfe;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .stats-icon {
            display: inline-block;
            background: #fff;
            border-radius: 50%;
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .stats-value {
            font-size: 28px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .stats-subtext {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .stats-card.green { background: linear-gradient(to bottom right, #ecfdf5, #bbf7d0); border-color: #86efac; }
        .stats-card.green .stats-value { color: #059669; }
        .stats-card.purple { background: linear-gradient(to bottom right, #f5f3ff, #ddd6fe); border-color: #c4b5fd; }
        .stats-card.purple .stats-value { color: #7c3aed; }

        .table-container {
            width: 100%;
            overflow-x: auto;
            background-color: rgba(255, 255, 255, 0.85);
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            box-sizing: border-box;
            overflow-x: visible;
        }

        table, th, td {
            font-size: 10px;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #D1D5DB;
            padding: 4px;
            word-wrap: break-word;
        }

        td.center {
            text-align: center;
        }

        .description-cell {
            max-width: 100px;
            word-wrap: break-word;
        }

        .changes-cell {
            max-width: 150px;
            word-wrap: break-word;
        }

        .change-field {
            font-size: 11px;
            margin-bottom: 2px;
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
        <div class="header">

            @if ($isAdmin)
                <div class="header-left">
                    <img src="{{ public_path('images/mandaue-logo.png') }}" alt="Mandaue Logo">
                </div>
                <div class="header-right">
                    <span class="span-1">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
                    <span class="span-2">ADMINISTRATION</span>
                </div>
            @else
                @php
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
                @endphp
                <div class="header-left">
                    <img src="{{ public_path('images/mandaue-logo.png') }}" alt="Mandaue Logo">
                    <img src="{{ $departmentLogo }}" alt="Department Logo">
                </div>
                <div class="header-right">
                    <span class="span-1">REPUBLIC OF THE PHILIPPINES | CITY OF MANDAUE</span>
                    <span class="span-2">{{ strtoupper($departmentName) }}</span>
                </div>
            @endif

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

        @if ($isAdmin)
            <div class="stats-grid">
                <div class="stats-card">
                    <div class="stats-icon">
                        <span style="font-size:24px;">👥</span>
                    </div>
                    <div class="stats-title">Total Users</div>
                    <div class="stats-value">{{ $totalUsers }}</div>
                </div>

                <div class="stats-card green">
                    <div class="stats-icon">
                        <span style="font-size:24px;">📶</span>
                    </div>
                    <div class="stats-title">Active Users</div>
                    <div class="stats-value">{{ $activeUsers }}</div>
                    <div class="stats-subtext">Online now (last 5 min)</div>
                </div>

                <div class="stats-card purple">
                    <div class="stats-icon">
                        <span style="font-size:24px;">⏱️</span>
                    </div>
                    <div class="stats-title">Total Online Time</div>
                    <div class="stats-value">{{ $totalOnlineTimeFormatted }}</div>
                    <div class="stats-subtext">Sum of all active users</div>
                </div>
            </div>
        @endif

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
                        <th>Description</th>
                        <th>Changes</th>
                        @if($isAdmin)
                            <th>User</th>
                            <th>Role</th>
                            <th>Location</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="center">{{ $log->activity_log_id }}</td>
                            <td>{{ $log->module ?? 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $log->action_type)) }}</td>
                            <td>{{ str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', $log->action))) }}</td>
                            <td class="center">{{ \Carbon\Carbon::parse($log->timestamp ?? $log->created_at)->format('M d, Y h:i A') }}</td>
                            <td>{{ $log->platform ?? 'N/A' }}</td>
                            <td class="description-cell">{{ $log->description ?? 'N/A' }}</td>
                            <td class="changes-cell">
                                @if($log->changes && is_array($log->changes))
                                    @foreach($log->changes as $field => $value)
                                        @continue($field === 'user_id')
                                        @php
                                            if (is_array($value) && isset($value['old'])) {
                                                $value['old'] = is_array($value['old']) ? implode(', ', $value['old']) : $value['old'];
                                            }
                                            if (is_array($value) && isset($value['new'])) {
                                                $value['new'] = is_array($value['new']) ? implode(', ', $value['new']) : $value['new'];
                                            }
                                        @endphp
                                        <div class="change-field">
                                            <strong>{{ strtoupper(str_replace('_', ' ', $field)) }}:</strong>
                                            @if(is_array($value))
                                                <span>OLD: {{ $value['old'] ?? '—' }}</span> |
                                                <span>NEW: {{ $value['new'] ?? '—' }}</span>
                                            @else
                                                <span>{{ $value }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <span>N/A</span>
                                @endif
                            </td>
                            @if($isAdmin)
                                <td>{{ $log->user?->name ?? 'N/A' }}</td>
                                <td>{{ $log->role?->name ? strtoupper(str_replace('_', ' ', $log->role->name)) : 'N/A' }}</td>
                                <td>{{ $log->location ?? 'Unknown' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 11 : 9 }}" style="text-align:center; font-style:italic; color:#6B7280;">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="status-footer">
            <div class="footer-row">
                <div class="footer-label">Noted by:</div>
                <div class="footer-value">{{ $user->name ?? 'N/A' }}</div>
            </div>
            <div class="footer-subtext">
                {{ $user->getRoleNames()->first()
                        ? ucwords(str_replace('_', ' ', $user->getRoleNames()->first()))
                        : 'N/A'
                    }}
            </div>
        </div>
    </div>
</body>
</html>
