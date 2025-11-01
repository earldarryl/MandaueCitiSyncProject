<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AdminLineChart extends ChartWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected ?string $heading = 'Average Age of Registered Users';

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
                        'color' => '#374151',
                        'font' => ['weight' => 'bold'],
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Average Age of Registered Users by Month',
                    'color' => '#1F2937',
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
                    'ticks' => [
                        'color' => '#4B5563',
                    ],
                    'grid' => [
                        'color' => 'rgba(107,114,128,0.2)',
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#4B5563',
                    ],
                    'grid' => [
                        'color' => 'rgba(107,114,128,0.1)',
                    ],
                ],
            ],
        ];
    }
}
