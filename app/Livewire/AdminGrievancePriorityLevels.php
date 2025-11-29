<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;

class AdminGrievancePriorityLevels extends ChartWidget
{
    protected static ?int $sort = 3;

    public $startDate;
    public $endDate;

    protected function getType(): string
    {
        return 'bar';
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
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <span>Reports by Priority Level</span>
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
                        This chart shows the total number of reports in the system,
                        categorized by <span class="font-semibold text-gray-800 dark:text-gray-200">Priority Levels</span>
                        such as Low, Normal, High, and Critical.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        Helps Admins identify the urgency of all reports and prioritize actions accordingly.
                    </div>
                </div>
            </div>
        HTML);
    }

    protected function getData(): array
    {
        $grievanceCounts = Grievance::query()
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->selectRaw("
                priority_level,
                COUNT(*) as total
            ")
            ->groupBy('priority_level')
            ->pluck('total', 'priority_level');

        $labels = ['Low', 'Normal', 'High', 'Critical'];
        $data = collect($labels)->map(fn($label) => $grievanceCounts[$label] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Reports',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.3)',
                        'rgba(107, 114, 128, 0.3)',
                        'rgba(251, 191, 36, 0.3)',
                        'rgba(239, 68, 68, 0.3)',
                    ],
                    'borderColor' => [
                        '#22c55e',
                        '#6b7280',
                        '#facc15',
                        '#ff0000ff',
                    ],
                    'borderWidth' => 3,
                    'barThickness' => 30,
                    'maxBarThickness' => 40,
                ],
            ],
            'labels' => array_map(fn($label) => ucfirst($label), $labels),
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
                'x' => ['ticks' => ['font' => ['size' => 13]]],
                'y' => ['beginAtZero' => true, 'ticks' => ['font' => ['size' => 13], 'precision' => 0]],
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
