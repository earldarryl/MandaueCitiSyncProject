<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrievanceBarChart extends ChartWidget
{
    protected ?string $heading = 'Grievances Assigned (Dynamic Time Scale)';
    protected static ?int $sort = 1;

    protected function getFilters(): ?array
    {
        $years = Grievance::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $filters = [];

        foreach ($years as $year) {
            $filters["year_{$year}"] = "Year {$year}";
        }

        $filters['scale_daily'] = 'By Day';
        $filters['scale_weekly'] = 'By Week';
        $filters['scale_monthly'] = 'By Month';

        return $filters;
    }

    /**
     * Build chart data based on selected scale
     */
    protected function getData(): array
    {
        $filter = $this->filter ?? 'scale_weekly';
        $year = now()->year;
        $scale = 'weekly';
        $userId = auth()->id();

        // Detect if year or scale filter is selected
        if (str_starts_with($filter, 'year_')) {
            $year = str_replace('year_', '', $filter);
        } elseif (str_starts_with($filter, 'scale_')) {
            $scale = str_replace('scale_', '', $filter);
        }

        // Start query — filter by HR liaison assignments
        $baseQuery = Grievance::whereHas('assignments', function ($query) use ($userId) {
            $query->where('hr_liaison_id', $userId);
        })->whereYear('created_at', $year);

        switch ($scale) {
            case 'daily':
                $grievances = $baseQuery
                    ->select(
                        DB::raw('DATE(created_at) as date_key'),
                        DB::raw('DATE_FORMAT(created_at, "%b %d") as date_label'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('date_key', 'date_label')
                    ->orderBy('date_key')
                    ->get();

                $labels = $grievances->pluck('date_label')->toArray();
                $data = $grievances->pluck('total')->toArray();
                $label = "Assigned Grievances per Day ({$year})";
                break;

            case 'monthly':
                $grievances = $baseQuery
                    ->select(
                        DB::raw('MONTH(created_at) as month_number'),
                        DB::raw('MONTHNAME(created_at) as month_label'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('month_number', 'month_label')
                    ->orderBy('month_number')
                    ->get();

                $labels = $grievances->pluck('month_label')->toArray();
                $data = $grievances->pluck('total')->toArray();
                $label = "Assigned Grievances per Month ({$year})";
                break;

            case 'weekly':
            default:
                $grievances = $baseQuery
                    ->select(
                        DB::raw('YEARWEEK(created_at, 1) as week_key'),
                        DB::raw('WEEK(created_at, 1) as week_number'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('week_key', 'week_number')
                    ->orderBy('week_key')
                    ->get();

                $labels = $grievances->pluck('week_number')->map(fn ($week) => 'Week ' . $week)->toArray();
                $data = $grievances->pluck('total')->toArray();
                $label = "Assigned Grievances per Week ({$year})";
                break;
        }

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

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'x',
            'responsive' => true,
            'maintainAspectRatio' => false,
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
