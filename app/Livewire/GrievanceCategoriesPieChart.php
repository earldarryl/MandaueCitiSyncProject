<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class GrievanceCategoriesPieChart extends ChartWidget
{
    protected static ?int $sort = 4;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $selectedType = null;

    public function mount($startDate = null, $endDate = null): void
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->format('Y-m-d');
    }

    public function getHeading(): string|Htmlable|null
    {
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                            </svg>
                            <span>Report Categories Distribution</span>
                        </h2>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="relative inline-block w-56">
                            <select
                                wire:model.live="selectedType"
                                id="grievanceType"
                                class="peer w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-700
                                    bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-800 dark:text-gray-200
                                    focus:border-blue-500 focus:ring-2 focus:ring-blue-400/60 outline-none shadow-sm
                                    transition-all duration-200 ease-in-out hover:shadow-md hover:border-blue-400"
                            >
                                <option value="">All Types</option>
                                <option value="Complaint">Complaint</option>
                                <option value="Request">Request</option>
                                <option value="Inquiry">Inquiry</option>
                            </select>

                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 peer-focus:text-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
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
                        This chart visualizes <span class="font-semibold text-gray-800 dark:text-gray-300">
                        all reports</span> within the selected date range, grouped by
                        their <span class="font-semibold text-gray-800 dark:text-gray-300">category</span> and type.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To help HR Liaisons quickly identify which report categories
                        have higher workload and prioritize actions accordingly.
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
            })
            ->when($this->selectedType, function ($query) {
                $query->where('grievance_type', $this->selectedType);
            });
    }

    protected function getData(): array
    {
        $query = $this->baseQuery();

        $categoryCounts = $query
            ->selectRaw('grievance_category, COUNT(*) as total')
            ->groupBy('grievance_category')
            ->pluck('total', 'grievance_category');

        $labels = $categoryCounts->keys()
            ->map(fn($c) => ucwords(str_replace('_', ' ', $c)))
            ->toArray();

        $data = $categoryCounts->values()->toArray();
        $total = array_sum($data);

        $labelsWithPercent = collect($labels)->map(function ($label, $i) use ($data, $total) {
            $count = $data[$i] ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return "{$label} ({$count} - {$percentage}%)";
        })->toArray();

        $colors = collect($labels)->map(function ($label) {
            $hash = crc32($label);
            $r = ($hash & 0xFF0000) >> 16;
            $g = ($hash & 0x00FF00) >> 8;
            $b = $hash & 0x0000FF;
            return "rgba($r, $g, $b, 0.85)";
        })->toArray();

        $borderColors = collect($colors)->map(fn($c) => str_replace('0.85', '1', $c))->toArray();

        return [
            'labels' => $labelsWithPercent,
            'datasets' => [
                [
                    'label' => 'Reports per Category',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $borderColors,
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
                    'font' => [
                        'size' => 11,
                        'weight' => '500',
                    ],
                    'boxWidth' => 12,
                    'boxHeight' => 12,
                    'padding' => 10,
                    'usePointStyle' => true,
                    'pointStyle' => 'circle',
                ],
            ],
            'tooltip' => [
                'backgroundColor' => 'rgba(0, 0, 0, 0.95)',
                'bodyColor' => '#e5e7eb',
                'cornerRadius' => 10,
                'padding' => 12,
                'titleFont' => ['size' => 14, 'weight' => 'bold'],
                'bodyFont' => ['size' => 14],
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
