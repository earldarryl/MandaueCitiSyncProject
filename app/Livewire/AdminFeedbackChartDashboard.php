<?php

namespace App\Livewire;

use App\Models\Feedback;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AdminFeedbackChartDashboard extends ChartWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount($startDate = null, $endDate = null): void
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->format('Y-m-d');
    }

    public function getHeading(): string|Htmlable|null
    {
        $totalFeedbacks = Feedback::whereBetween('created_at', [
            $this->startDate . ' 00:00:00',
            $this->endDate . ' 23:59:59',
        ])->count();

        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-700 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-6 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h6M6 12v6h6"/>
                            </svg>
                            <span>User Satisfaction Summary</span>
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
                        This pie chart shows the distribution of user feedback categorized as
                        <span class="font-semibold text-green-600">Satisfied</span> or
                        <span class="font-semibold text-red-600">Dissatisfied</span> users
                        within the selected date range
                        (<strong>{$this->startDate}</strong> to <strong>{$this->endDate}</strong>).
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Total feedbacks:</span> {$totalFeedbacks}.
                        <br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To help admins assess both satisfaction and dissatisfaction levels.
                    </div>
                </div>
            </div>
        HTML);
    }

    protected function getData(): array
    {
        $query = Feedback::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
        }

        $feedbacks = $query->get();
        $totalFeedbacks = $feedbacks->count();

        $satisfiedCount = 0;
        $dissatisfiedCount = 0;

        foreach ($feedbacks as $feedback) {
            $summary = $this->summarizeSQD($feedback);
            if ($summary === 'Most Agree') {
                $satisfiedCount++;
            } elseif ($summary === 'Most Disagree') {
                $dissatisfiedCount++;
            }
        }

        $satisfiedPercent = $totalFeedbacks > 0 ? round(($satisfiedCount / $totalFeedbacks) * 100, 1) : 0;
        $dissatisfiedPercent = $totalFeedbacks > 0 ? round(($dissatisfiedCount / $totalFeedbacks) * 100, 1) : 0;

        return [
            'labels' => [
                "Satisfied Users ({$satisfiedCount} - {$satisfiedPercent}%)",
                "Dissatisfied Users ({$dissatisfiedCount} - {$dissatisfiedPercent}%)",
            ],
            'datasets' => [
                [
                    'label' => 'User Feedback Distribution',
                    'data' => [$satisfiedCount, $dissatisfiedCount],
                    'backgroundColor' => ['#10B981', '#EF4444'],
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

    protected function summarizeSQD($feedback): string
    {
        $answers = is_array($feedback->answers)
            ? $feedback->answers
            : (is_string($feedback->answers) ? json_decode($feedback->answers, true) : []);

        if (empty($answers)) {
            return 'Neutral';
        }

        $counts = array_count_values($answers);

        $categories = [
            'Strongly Disagree' => $counts[1] ?? 0,
            'Disagree' => $counts[2] ?? 0,
            'Neither' => $counts[3] ?? 0,
            'Agree' => $counts[4] ?? 0,
            'Strongly Agree' => $counts[5] ?? 0,
        ];

        $maxCount = max($categories);
        $dominant = array_keys($categories, $maxCount);

        if (in_array('Strongly Agree', $dominant) || in_array('Agree', $dominant)) return 'Most Agree';
        if (in_array('Strongly Disagree', $dominant) || in_array('Disagree', $dominant)) return 'Most Disagree';
        if (in_array('Neither', $dominant)) return 'Neutral';

        return 'Neutral';
    }
}
