<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class GrievanceStatusesPolarChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount($startDate = null, $endDate = null): void
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->format('Y-m-d');
    }

    public function getHeading(): string | Htmlable | null
    {
        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-700 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="size-6 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Report Status Overview (Dynamic Time Range)</span>
                        </h2>
                    </div>
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

                    <div x-show="open" x-collapse
                        class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-zinc-800
                            rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                        This chart visualizes <span class="font-semibold text-gray-800 dark:text-gray-300">
                        all reports assigned to you</span> within the selected date range,
                        grouped by their <span class="font-semibold text-gray-800 dark:text-gray-300">current status</span>.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To help HR Liaisons quickly assess workload distribution, identify backlog areas,
                        and monitor progress across different report statuses over time.
                    </div>
                </div>
            </div>
        HTML);
    }

    protected function baseQuery()
    {
        $user = Auth::user();

        return Grievance::query()
            ->when($user->hasRole('hr_liaison'), function ($query) use ($user) {
                $query->whereHas('assignments', function ($q) use ($user) {
                    $q->where('hr_liaison_id', $user->id);
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59',
                ]);
            });
    }

    protected function getData(): array
    {
        $query = $this->baseQuery();

        $statusCounts = $query
            ->selectRaw('grievance_status, COUNT(*) as total')
            ->groupBy('grievance_status')
            ->pluck('total', 'grievance_status');

        $labels = ['pending', 'acknowledged', 'in_progress', 'escalated', 'resolved', 'unresolved', 'closed', 'overdue'];
        $formattedLabels = collect($labels)->map(fn($s) => ucwords(str_replace('_', ' ', $s)))->toArray();

        $data = collect($labels)->map(fn($status) => $statusCounts[$status] ?? 0)->toArray();
        $total = array_sum($data);

        $labelsWithPercent = collect($formattedLabels)->map(function ($label, $i) use ($data, $total) {
            $count = $data[$i] ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return "{$label} ({$count} - {$percentage}%)";
        })->toArray();

        $colors = [
            'rgba(251, 191, 36, 0.85)',
            'rgba(56, 189, 248, 0.85)',
            'rgba(59, 130, 246, 0.85)',
            'rgba(168, 85, 247, 0.85)',
            'rgba(16, 185, 129, 0.85)',
            'rgba(239, 68, 68, 0.85)',
            'rgba(107, 114, 128, 0.85)',
            'rgba(252, 42, 0, 0.98)',
        ];

        $borders = [
            '#fbbf24', '#38bdf8', '#3b82f6', '#a855f7', '#10b981', '#ef4444', '#6b7280', '#ff0000ff'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Report Status Distribution',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $borders,
                    'borderWidth' => 3,
                ],
            ],
            'labels' => $labelsWithPercent,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'align' => 'center',
                    'labels' => [
                        'font' => [
                            'size' => 11,
                            'weight' => '500',
                        ],
                        'boxWidth' => 12,
                        'boxHeight' => 12,
                        'padding' => 8,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.95)',
                    'bodyColor' => '#e5e7eb',
                    'cornerRadius' => 10,
                    'padding' => 10,
                    'titleFont' => ['size' => 12, 'weight' => 'bold'],
                    'bodyFont' => ['size' => 12],
                ],
            ],
            'scales' => [
                'r' => [
                    'grid' => ['color' => 'rgba(107,114,128,0.3)'],
                    'ticks' => [
                        'color' => '#9ca3af',
                        'backdropColor' => 'transparent',
                        'font' => ['size' => 10],
                    ],
                ],
            ],
            'layout' => [
                'padding' => ['top' => 5, 'bottom' => 5, 'right' => 5, 'left' => 5],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
