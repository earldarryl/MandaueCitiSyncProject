<?php

namespace App\Livewire;

use App\Models\UserInfo;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class AdminBarChart extends ChartWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function getHeading(): string | HtmlString | null
    {
        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full p-3">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-700 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="size-6 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.5 3.75h15a.75.75 0 0 1 .75.75v15a.75.75 0 0 1-.75.75h-15A.75.75 0 0 1 3.75 19.5V4.5a.75.75 0 0 1 .75-.75zm3 12h9m-9-4.5h9m-9-4.5h9" />
                            </svg>
                            <span>Registered Users per Barangay</span>
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
                        This bar chart displays the number of
                        <span class="font-semibold text-gray-800 dark:text-gray-300">registered users</span>
                        across different barangays in Mandaue City.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To visualize population distribution and identify barangays with higher registration activity.
                    </div>
                </div>
            </div>
        HTML);
    }

    protected function getData(): array
    {
        $fullBarangays = [
            'Alang-Alang','Bakilid','Banilad','Basak','Cabancalan','Cambaro','Canduman',
            'Casili','Casuntingan','Centro','Cubacub','Guizo','Ibabao-Estancia','Jagobiao',
            'Labogon','Looc','Maguikay','Mantuyong','Opao','Pagsabungan','Pakna-an',
            'Subangdaku','Tabok','Tawason','Tingub','Tipolo','Umapad',
        ];

        $barangayData = UserInfo::query()
            ->join('users', 'user_infos.user_id', '=', 'users.id')
            ->when($this->startDate, fn($q) =>
                $q->whereDate('users.created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) =>
                $q->whereDate('users.created_at', '<=', $this->endDate))
            ->select('user_infos.barangay', DB::raw('COUNT(user_infos.id) as total'))
            ->groupBy('user_infos.barangay')
            ->orderBy('user_infos.barangay', 'asc')
            ->get();

        $dataMap = $barangayData->pluck('total', 'barangay')->toArray();

        $labels = [];
        $values = [];
        foreach ($fullBarangays as $barangay) {
            $labels[] = $barangay;
            $values[] = $dataMap[$barangay] ?? 0;
        }

        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#06B6D4', '#84CC16', '#EC4899', '#14B8A6', '#F97316',
            '#6366F1', '#22C55E', '#EAB308', '#DC2626', '#A855F7',
            '#0EA5E9', '#65A30D', '#BE185D', '#0D9488', '#EA580C',
            '#2563EB', '#16A34A', '#CA8A04', '#B91C1C', '#7C3AED',
            '#0284C7', '#15803D'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Registered Users',
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderColor' => '#1E3A8A',
                    'borderWidth' => 1,
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
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'labels' => [
                        'font' => ['weight' => 'bold'],
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Registered Users per Barangay - Mandaue City',
                    'font' => ['size' => 16, 'weight' => '600'],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'cornerRadius' => 8,
                    'padding' => 8,
                    'titleFont' => ['size' => 14, 'weight' => 'bold'],
                    'bodyFont' => ['size' => 12],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Total Users',
                    ],
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'autoSkip' => false,
                        'maxRotation' => 45,
                        'minRotation' => 0,
                        'font' => ['size' => 11],
                    ],
                ],
            ],
        ];
    }
}
