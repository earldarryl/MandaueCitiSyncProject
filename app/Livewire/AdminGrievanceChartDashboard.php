<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AdminGrievanceChartDashboard extends ChartWidget
{
    protected static ?int $sort = 1;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $selectedType = null;

    public function mount($startDate = null, $endDate = null): void
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->format('Y-m-d');
        $this->selectedType = null;
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
                            <span>Admin Grievance Type Overview</span>
                        </h2>
                    </div>

                    <div class="relative inline-block w-48">
                        <select wire:model.live="selectedType"
                            class="peer w-full appearance-none rounded-xl border border-gray-300 dark:border-gray-700
                            bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-800 dark:text-gray-200
                            focus:border-blue-500 focus:ring-2 focus:ring-blue-400/60 outline-none shadow-sm">
                            <option value="">All Types</option>
                            <option value="Complaint">Complaint</option>
                            <option value="Inquiry">Inquiry</option>
                            <option value="Request">Request</option>
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
                        This pie chart shows the distribution of <span class="font-semibold text-gray-800 dark:text-gray-300">
                        grievances by type</span> (Complaint, Inquiry, Request) within the selected date range
                        (<strong>{$this->startDate}</strong> to <strong>{$this->endDate}</strong>).
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To help admins understand the proportion of each grievance type,
                        monitor trends, and prioritize handling accordingly.
                    </div>
                </div>
            </div>
        HTML);
    }

    protected function baseQuery()
    {
        return Grievance::query()
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

        $counts = $query
            ->selectRaw('grievance_type, grievance_category, COUNT(*) as total')
            ->groupBy('grievance_type', 'grievance_category')
            ->get();

        $data = $counts->pluck('total')->toArray();
        $total = array_sum($data);

        $labels = $counts->map(function ($row, $i) use ($data, $total) {
            $count = $data[$i] ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return "{$row->grievance_type} - {$row->grievance_category} ({$count} - {$percentage}%)";
        })->toArray();

        $colors = $counts->map(function ($row) {
            return match($row->grievance_type) {
                'Complaint' => 'rgba(239, 68, 68, 0.85)',
                'Inquiry'   => 'rgba(59, 130, 246, 0.85)',
                'Request'   => 'rgba(16, 185, 129, 0.85)',
                default     => 'rgba(107, 114, 128, 0.85)',
            };
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Grievance Type & Category Distribution',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
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
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled' => true,
                    'cornerRadius' => 8,
                    'padding' => 8,
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold',
                    ],
                    'bodyFont' => [
                        'size' => 12,
                    ],
                ],
            ],
            'layout' => [
                'padding' => [
                    'top' => 10,
                    'bottom' => 10,
                    'right' => 10,
                    'left' => 10,
                ],
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
