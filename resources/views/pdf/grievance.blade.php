<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grievance #{{ $grievance->grievance_id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1, h2, h3 { color: #0d47a1; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { padding: 6px; border: 1px solid #ccc; vertical-align: top; }
        .section { margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Grievance Report</h1>

    <div class="section">
        <p><strong>ID:</strong> #{{ $grievance->grievance_id }}</p>
        <p><strong>Title:</strong> {{ $grievance->grievance_title }}</p>
        <p><strong>Type:</strong> {{ $grievance->grievance_type }}</p>
        <p><strong>Priority:</strong> {{ ucfirst($grievance->priority_level) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($grievance->grievance_status) }}</p>
        <p><strong>Filed On:</strong> {{ $grievance->created_at->format('M d, Y h:i A') }}</p>
    </div>

    <div class="section">
        <h3>Details</h3>
        <p>{!! $grievance->grievance_details !!}</p>
    </div>

    <div class="section">
        <h3>Departments</h3>
        @forelse ($grievance->departments as $department)
            <p>- {{ $department->department_name }}</p>
        @empty
            <p><em>No department assigned</em></p>
        @endforelse
    </div>
</body>
</html>
