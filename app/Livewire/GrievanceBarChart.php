<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
class GrievanceBarChart extends ChartWidget
{
    protected static ?int $sort = 1;

    public $startDate;
    public $endDate;

    public function getHeading(): string | Htmlable | null
    {
        return new HtmlString('
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    📊 Grievances Assigned (Dynamic Time Scale)
                </h2>
                <button
                    wire:click="$refresh"
                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    Refresh
                </button>
            </div>
        ');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $userId = auth()->id();

        $baseQuery = Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $userId))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));

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

        return [
            'datasets' => [
                [
                    'label' => 'Assigned Grievances',
                    'data' => $data,
                    'backgroundColor' => '#2563eb',
                    'borderColor' => '#2563eb',
                    'borderWidth' => 3,
                    'barThickness' => 60,
                    'maxBarThickness' => 80,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#f9fafb',
                    'bodyColor' => '#e5e7eb',
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                    'cornerRadius' => 10,
                    'padding' => 12,
                    'displayColors' => true,
                    'usePointStyle' => true,
                    'caretPadding' => 8,
                    'caretSize' => 6,
                    'boxPadding' => 6,
                    'titleFont' => [
                        'size' => 15,
                        'weight' => 'bold',
                        'lineHeight' => 1.4,
                    ],
                    'bodyFont' => [
                        'size' => 13,
                        'lineHeight' => 1.3,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'font' => ['size' => 13],
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'font' => ['size' => 13],
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
