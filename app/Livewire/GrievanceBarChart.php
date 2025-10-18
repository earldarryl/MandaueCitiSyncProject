<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrievanceBarChart extends ChartWidget
{
    protected ?string $heading = 'Grievances Assigned (Dynamic Time Scale)';
    protected static ?int $sort = 1;

    // Accept start and end dates as props
    public $startDate;
    public $endDate;

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Build chart data based on selected scale (daily/weekly/monthly) within start and end date
     */
    protected function getData(): array
    {
        $userId = auth()->id();

        $baseQuery = Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $userId))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));

        // Default grouping: weekly
        $grievances = $baseQuery
            ->select(
                DB::raw('YEARWEEK(created_at, 1) as week_key'),
                DB::raw('WEEK(created_at, 1) as week_number'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('week_key', 'week_number')
            ->orderBy('week_key')
            ->get();

        $labels = $grievances->pluck('week_number')->map(fn($week) => 'Week ' . $week)->toArray();
        $data = $grievances->pluck('total')->toArray();
        $label = "Assigned Grievances";

        return [
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.4)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'barPercentage' => 0.5,
                    'barThickness' => 40,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'x',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'height' => 600,
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => ['color' => '#6b7280'],
                    'grid' => ['color' => 'rgba(156, 163, 175, 0.2)'],
                    'title' => [
                        'display' => true,
                        'text' => 'Time Period',
                        'color' => '#374151',
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['color' => '#6b7280'],
                    'grid' => ['display' => false],
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Assigned Grievances',
                        'color' => '#374151',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'labels' => ['color' => '#374151'],
                ],
                'tooltip' => [
                    'backgroundColor' => '#1f2937',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                ],
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
