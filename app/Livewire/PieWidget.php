<?php

namespace App\Livewire;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget as BaseChartWidget;
use App\Models\Grievance;
use Carbon\Carbon;

class PieWidget extends BaseChartWidget
{
    public $startDate;
    public $endDate;

    protected ?string $heading = 'Grievance Status Overview';
    protected $listeners = ['dateRangeUpdated'];

    protected static ?int $contentHeight = 400;

    public function dateRangeUpdated($start, $end)
    {
        $this->startDate = $start;
        $this->endDate   = $end;
        $this->updateChartData();
    }

    protected function getData(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->subDays(30);
        $end   = $this->endDate ? Carbon::parse($this->endDate)->endOfDay()       : now();

        $statuses = [
            'pending'     => 'Pending',
            'rejected'    => 'Rejected',
            'in_progress' => 'In Progress',
            'resolved'    => 'Resolved',
        ];

        $query = Grievance::query()->whereBetween('created_at', [$start, $end]);

        $user = auth()->user();
        if ($user->hasRole('hr_liaison')) {
            $query->whereHas('assignments', function ($q) use ($user) {
                $q->where('hr_liaison_id', $user->id);
            });
        }

        $counts = collect($statuses)->map(fn ($label, $key) =>
            (clone $query)->where('grievance_status', $key)->count()
        )->values()->toArray();

        return [
            'labels' => array_values($statuses),
            'datasets' => [[
                'label' => 'Grievances',
                'data' => $counts,
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
                'hoverOffset' => 10,
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week'  => 'Last week',
            'month' => 'Last month',
            'year'  => 'This year',
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
            {
                responsive: true,
                maintainAspectRatio: false,
            }
        JS);
    }
}
