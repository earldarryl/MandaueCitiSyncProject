<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget as BaseChartWidget;
use App\Models\User;
use Carbon\Carbon;

class ChartWidget extends BaseChartWidget
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
        $labels = [];
        $data = [];

        $start = $this->startDate ? Carbon::parse($this->startDate) : now()->subDays(6);
        $end = $this->endDate ? Carbon::parse($this->endDate) : now();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $labels[] = $date->format('M d');
            $data[] = User::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.4)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'options' => [
                'maintainAspectRatio' => false,
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
