<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget as BaseChartWidget;
use App\Models\Grievance;
use Carbon\Carbon;

class PieWidget extends BaseChartWidget
{
    public $startDate;
    public $endDate;

    protected ?string $heading = 'Grievance Status Overview';

    // 👂 Listen for emitted event
    protected $listeners = ['dateRangeUpdated'];

    public function dateRangeUpdated($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
    }

    protected function getData(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate) : now()->subDays(30);
        $end = $this->endDate ? Carbon::parse($this->endDate) : now();

        $pending = Grievance::where('grievance_status', 'pending')
            ->whereBetween('created_at', [$start, $end])->count();

        $rejected = Grievance::where('grievance_status', 'rejected')
            ->whereBetween('created_at', [$start, $end])->count();

        $inProgress = Grievance::where('grievance_status', 'in_progress')
            ->whereBetween('created_at', [$start, $end])->count();

        $resolved = Grievance::where('grievance_status', 'resolved')
            ->whereBetween('created_at', [$start, $end])->count();

        return [
            'labels' => ['Pending', 'Rejected', 'In Progress', 'Resolved'],
            'datasets' => [
                [
                    'label' => 'Grievances',
                    'data' => [$pending, $rejected, $inProgress, $resolved],
                    'backgroundColor' => [
                        'rgba(253, 224, 71, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                    ],
                    'borderColor' => [
                        'rgba(253, 224, 71, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'layout' => [
                    'padding' => 20,
                ],
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'align' => 'center',
                    ],
                ],
            ],
        ];
    }


    protected function getType(): string
    {
        return 'pie';
    }
}
