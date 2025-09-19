<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget as BaseChartWidget;
use App\Models\User;
use Carbon\Carbon;

class BarWidget extends BaseChartWidget
{
    public $startDate;
    public $endDate;
    protected ?string $heading = 'Users Registered';
    protected $listeners = ['dateRangeUpdated'];

    public function dateRangeUpdated($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
    }

    protected function getData(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate) : Carbon::now()->subDays(6);
        $end   = $this->endDate ? Carbon::parse($this->endDate) : Carbon::now();

        $users = User::whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $period = \Carbon\CarbonPeriod::create($start, $end);
        $labels = [];
        $data = [];
        foreach ($period as $date) {
            $labels[] = $date->format('M d'); // e.g., Sep 19
            $data[] = $users[$date->format('Y-m-d')] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Users Registered',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
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

    // must be public
    public function getColumnSpan(): int|string|array
    {
        return 'full'; // full width
    }

    // must be public
    public function getContentHeight(): string
    {
        $labelCount = count($this->getData()['labels'] ?? []);
        $height = 300 + ($labelCount * 10); // base height + growth

        return $height . 'px';
    }

    // must be public
    public function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
