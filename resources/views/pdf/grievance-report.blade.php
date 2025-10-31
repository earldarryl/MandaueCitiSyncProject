<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grievance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #1f2937;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 16px;
        }
        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
        }
        .statuses {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .status-box {
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            color: #fff;
            min-width: 100px;
        }
        .pending { background-color: #f59e0b; } /* amber-500 */
        .delayed { background-color: #dc2626; } /* red-600 */
        .resolved { background-color: #22c55e; } /* green-500 */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #d1d5db; /* Tailwind gray-300 */
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6; /* Tailwind gray-100 */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
</head>
<body>

    <h1>Grievance Report</h1>
    <h2>{{ $startDate }} to {{ $endDate }}</h2>

    <div class="chart-container">
        <canvas id="grievanceChart" width="600" height="400"></canvas>
    </div>

    <div class="statuses">
        <div class="status-box pending">
            Pending: {{ $statuses['Pending'] ?? 0 }}
        </div>
        <div class="status-box delayed">
            Delayed: {{ $statuses['Delayed'] ?? 0 }}
        </div>
        <div class="status-box resolved">
            Resolved: {{ $statuses['Resolved'] ?? 0 }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Category</th>
                <th>Status</th>
                <th>Processing Days</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->grievance_ticket_id }}</td>
                    <td>{{ $item->grievance_title }}</td>
                    <td>{{ $item->grievance_type ?? '-' }}</td>
                    <td>{{ $item->grievance_category }}</td>
                    <td>
                        @if($item->grievance_status === 'Resolved')
                            Resolved
                        @elseif($item->grievance_status === 'Pending' && $item->processing_days !== null && now()->diffInDays($item->created_at) > $item->processing_days)
                            Delayed
                        @else
                            Pending
                        @endif
                    </td>
                    <td>{{ $item->processing_days ?? '—' }}</td>
                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        Chart.register(ChartDataLabels);

        const statusCounts = @json($statuses ?? ['Pending' => 0, 'Delayed' => 0, 'Resolved' => 0]);
        const labels = Object.keys(statusCounts);
        const chartData = Object.values(statusCounts);

        const backgroundColors = [
            'rgba(255, 193, 7, 0.6)',
            'rgba(220, 38, 38, 0.6)',
            'rgba(34, 197, 94, 0.6)'
        ];

        const borderColors = [
            'rgba(255, 193, 7, 1)',
            'rgba(220, 38, 38, 1)',
            'rgba(34, 197, 94, 1)'
        ];

        const data = {
            labels: labels,
            datasets: [{
                label: 'Grievances by Status',
                data: chartData,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        };

        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12 } } },
                    title: { display: true, text: 'Grievances by Status', font: { size: 16 } },
                    datalabels: {
                        color: '#000',
                        font: { weight: 'bold', size: 12 },
                        formatter: (value, context) => {
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${context.chart.data.labels[context.dataIndex]}: ${value} (${percentage}%)`;
                        }
                    }
                },
            },
            plugins: [ChartDataLabels]
        };

        new Chart(document.getElementById('grievanceChart').getContext('2d'), config);
    </script>

</body>
</html>
