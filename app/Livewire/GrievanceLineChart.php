<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class GrievanceLineChart extends ChartWidget
{
    protected static ?int $sort = 1;

    public $startDate;
    public $endDate;
    protected float|null $percentChange = null;
    protected bool $isIncrease = false;

    public function getHeading(): string | Htmlable | null
    {
        $changeHtml = '';

        if (!is_null($this->percentChange)) {
            $color = $this->isIncrease ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
            $arrow = $this->isIncrease ? '↑' : '↓';
            $percent = number_format(abs($this->percentChange), 1) . '%';
            $changeHtml = <<<HTML
                <div class="flex items-center gap-1 text-md font-bold {$color}">
                    <span>{$arrow}</span>
                    <span>{$percent}</span>
                </div>
            HTML;
        }

        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-700 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />
                            </svg>
                            <span>Grievances Assigned (Dynamic Time Scale)</span>
                        </h2>
                    </div>
                    {$changeHtml}
                </div>

                <div x-data="{ open: false }" class="mt-1">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-sm font-medium text-gray-700 dark:text-gray-200
                            hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <span>About this chart</span>
                        <svg xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-180': open }"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-zinc-800 rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                        This line chart tracks the number of grievances assigned to HR Liaisons over a selected time range.
                        Each point represents a weekly total, allowing users to visualize performance and detect spikes or drops.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To help HR Liaisons and management monitor how grievance assignments evolve over time,
                        evaluate consistency in case handling, and identify patterns that may require further attention.
                    </div>
                </div>
            </div>
        HTML);
    }

    protected function getType(): string
    {
        return 'line';
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

        if (count($data) >= 2) {
            $previous = $data[count($data) - 2];
            $current = $data[count($data) - 1];
            if ($previous > 0) {
                $this->percentChange = (($current - $previous) / $previous) * 100;
                $this->isIncrease = $this->percentChange > 0;
            } else {
                $this->percentChange = null;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Assigned Grievances (Weekly)',
                    'data' => $data,
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#2563eb',
                    'pointBorderColor' => '#ffffff',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 7,
                    'borderWidth' => 3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#f9fafb',
                    'bodyColor' => '#e5e7eb',
                    'cornerRadius' => 10,
                    'padding' => 12,
                    'titleFont' => ['size' => 15, 'weight' => 'bold'],
                    'bodyFont' => ['size' => 13],
                ],
                'legend' => [
                    'labels' => [
                        'font' => ['size' => 13],
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['font' => ['size' => 13]],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['font' => ['size' => 13], 'precision' => 0],
                ],
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
