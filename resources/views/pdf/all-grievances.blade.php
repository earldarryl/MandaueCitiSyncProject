<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Grievances Assigned</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 10px; }
        p { text-align: center; font-size: 12px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>All Grievances Assigned</h1>
    <p><strong>HR Liaison:</strong> {{ $hr_liaison->name }}</p>
    <p><strong>Generated on:</strong> {{ now()->format('F d, Y h:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Complainant</th>
                <th>Department(s)</th>
                <th>Title</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Date Filed</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grievances as $g)
                <tr>
                    <td>{{ $g->grievance_id }}</td>
                    <td>
                        @if ($g->is_anonymous)
                            Anonymous
                        @elseif ($g->user?->info)
                            {{ $g->user->info->first_name }} {{ $g->user->info->last_name }}
                        @else
                            {{ $g->user?->name ?? 'N/A' }}
                        @endif
                    </td>
                    <td>{{ $g->departments->pluck('department_name')->join(', ') ?: 'N/A' }}</td>
                    <td>{{ $g->grievance_title }}</td>
                    <td>{{ $g->grievance_type }}</td>
                    <td>{{ $g->priority_level }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $g->grievance_status)) }}</td>
                    <td>{{ $g->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
