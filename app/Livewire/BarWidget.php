<?php

namespace App\Livewire;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget as BaseChartWidget;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BarWidget extends BaseChartWidget
{
    public $startDate;
    public $endDate;

    protected ?string $heading = 'Users Registered';
    protected $listeners = ['dateRangeUpdated'];

    protected static ?int $contentHeight = 400; // Height in pixels

    public function dateRangeUpdated($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
        $this->updateChartData(); // Added to refresh chart on date change
    }

    protected function getData(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate) : now()->subDays(6);
        $end = $this->endDate ? Carbon::parse($this->endDate) : now();

        $users = User::whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $period = CarbonPeriod::create($start, $end);

        $labels = [];
        $data = [];
        foreach ($period as $date) {
            $labels[] = $date->format('M d');
            $data[] = $users[$date->format('Y-m-d')] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Users Registered',
                    'data' => $data,
                    'backgroundColor' => 'rgba(37, 99, 235, 0.7)',
                    'borderColor' => 'rgba(37, 99, 235, 1)',
                    'borderWidth' => 2,
                    'width' => 10,
                    'height'=> 10,
                    'barPercentage' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => value,
                        },
                    },
                },
            }
        JS);
    }
}
