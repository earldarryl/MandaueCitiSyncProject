<?php

namespace App\Livewire;

use App\Models\Feedback;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AdminFeedbackChartDashboard extends ChartWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $selectedFilter = null;
    public array $filterOptions = [];

    public function mount($startDate = null, $endDate = null): void
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->format('Y-m-d');

        $this->filterOptions = $this->getOptionLabels();
    }


    public function getHeading(): string|Htmlable|null
    {
        $options = $this->filterOptions;

        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-700 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="size-6 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h6M6 12v6h6"/>
                            </svg>
                            <span>User Feedback Summary</span>
                        </h2>
                    </div>

                    <div class="relative inline-block w-56">
                        <select wire:model.live="selectedFilter"
                            class="peer w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-700
                            bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-800 dark:text-gray-200
                            focus:border-blue-500 focus:ring-2 focus:ring-blue-400/60 outline-none shadow-sm">
                            <option value="Satisfaction">Satisfaction</option>
                            <option value="Awareness">Awareness</option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center text-gray-500 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
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
                        This pie chart visualizes user feedback within the selected date range,
                        filtered by the selected <span class="font-semibold text-green-600">Satisfaction</span>
                        or <span class="font-semibold text-blue-600">Awareness</span> level.
                    </div>
                </div>
            </div>
    HTML);
    }

    public function getOptionLabels(): array
    {
        $base = Feedback::query()
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59',
                ]);
            })
            ->get();

        return [
            'Most Agree'       => 'Satisfied (' . $base->where('sqd_summary', 'Most Agree')->count() . ')',
            'Most Disagree'    => 'Dissatisfied (' . $base->where('sqd_summary', 'Most Disagree')->count() . ')',
            'Neutral'          => 'Neutral (' . $base->where('sqd_summary', 'Neutral')->count() . ')',
            'High Awareness'   => 'High Awareness (' . $base->where('cc_summary', 'High Awareness')->count() . ')',
            'Medium Awareness' => 'Medium Awareness (' . $base->where('cc_summary', 'Medium Awareness')->count() . ')',
            'Low Awareness'    => 'Low Awareness (' . $base->where('cc_summary', 'Low Awareness')->count() . ')',
            'No Awareness'     => 'No Awareness (' . $base->where('cc_summary', 'No Awareness')->count() . ')',
        ];
    }

    protected function baseQuery()
    {
        return Feedback::query()
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59',
                ]);
            })
            ->when($this->selectedFilter, function ($query) {
                if ($this->selectedFilter === 'Satisfaction') {
                    return $query->whereIn('sqd_summary', ['Most Agree', 'Most Disagree', 'Neutral']);
                }

                if ($this->selectedFilter === 'Awareness') {
                    return $query->whereIn('cc_summary', ['High Awareness', 'Medium Awareness', 'Low Awareness', 'No Awareness']);
                }
            });
    }

    protected function getData(): array
    {
        $feedbacks = $this->baseQuery()->get();

        if ($this->selectedFilter === 'Awareness') {
            $high = $feedbacks->where('cc_summary', 'High Awareness')->count();
            $medium = $feedbacks->where('cc_summary', 'Medium Awareness')->count();
            $low = $feedbacks->where('cc_summary', 'Low Awareness')->count();
            $none = $feedbacks->where('cc_summary', 'No Awareness')->count();

            return [
                'labels' => [
                    "High Awareness ($high)",
                    "Medium Awareness ($medium)",
                    "Low Awareness ($low)",
                    "No Awareness ($none)",
                ],
                'datasets' => [
                    [
                        'label' => 'Awareness Distribution',
                        'data' => [$high, $medium, $low, $none],
                        'backgroundColor' => ['#10B981', '#3B82F6', '#FACC15', '#EF4444'],
                        'borderColor' => '#fff',
                        'borderWidth' => 2,
                    ],
                ],
            ];
        }

        $satisfied = $feedbacks->where('sqd_summary', 'Most Agree')->count();
        $dissatisfied = $feedbacks->where('sqd_summary', 'Most Disagree')->count();
        $neutral = $feedbacks->where('sqd_summary', 'Neutral')->count();

        return [
            'labels' => [
                "Satisfied ($satisfied)",
                "Dissatisfied ($dissatisfied)",
                "Neutral ($neutral)",
            ],
            'datasets' => [
                [
                    'label' => 'Satisfaction Distribution',
                    'data' => [$satisfied, $dissatisfied, $neutral],
                    'backgroundColor' => ['#10B981', '#EF4444', '#FBBF24'],
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                ],
            ],
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
                        'font' => ['size' => 14, 'weight' => '600'],
                        'boxWidth' => 16,
                        'boxHeight' => 16,
                        'padding' => 12,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'cornerRadius' => 8,
                    'padding' => 8,
                    'titleFont' => ['size' => 14, 'weight' => 'bold'],
                    'bodyFont' => ['size' => 12],
                ],
            ],
            'layout' => [
                'padding' => ['top' => 10, 'bottom' => 10, 'right' => 10, 'left' => 10],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
