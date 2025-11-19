<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
class AdminLineChart extends ChartWidget
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
                                    d="M3 3v18h18M9 9c0-1.657 1.343-3 3-3s3 1.343 3 3m0 0c0 1.657-1.343 3-3 3m0-3H3m6 3v6m6-6v6" />
                            </svg>
                            <span>Average Age of Registered Users</span>
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
                        This line chart visualizes the <span class="font-semibold text-gray-800 dark:text-gray-300">
                        average age</span> of all registered users each month.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To analyze demographic trends over time and identify growth in specific age groups among new users.
                    </div>
                </div>
            </div>
        HTML);
    }
    protected function getData(): array
    {
        $averageAges = User::query()
            ->whereHas('userInfo')
            ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
            ->when($this->startDate, fn($q) =>
                $q->whereDate('users.created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) =>
                $q->whereDate('users.created_at', '<=', $this->endDate))
            ->selectRaw('DATE_FORMAT(users.created_at, "%Y-%m") as month, AVG(user_infos.age) as avg_age')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $averageAges->pluck('month')->map(fn ($m) => date('F Y', strtotime($m)))->toArray();
        $data = $averageAges->pluck('avg_age')->map(fn ($age) => round($age, 1))->toArray();

        return [
            'datasets' => [[
                'label' => 'Average Age',
                'data' => $data,
                'borderColor' => '#3B82F6',
                'backgroundColor' => 'rgba(59,130,246,0.15)',
                'tension' => 0.3,
                'pointRadius' => 4,
                'pointBackgroundColor' => '#2563EB',
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'labels' => [
                        'font' => ['weight' => 'bold'],
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Average Age of Registered Users by Month',
                    'font' => ['size' => 16, 'weight' => '600'],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Average Age',
                    ],
                ],
            ],
        ];
    }
}
