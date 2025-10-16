<?php

namespace App\Livewire;

use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget as BaseChartWidget;
use App\Models\User;
use App\Models\Grievance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class BarWidget extends BaseChartWidget
{
    public $startDate;
    public $endDate;

    protected $listeners = ['dateRangeUpdated'];

    protected static ?int $contentHeight = 800;

    public function dateRangeUpdated($start, $end)
    {
        $this->startDate = $start;
        $this->endDate   = $end;
        $this->updateChartData();
    }

    public function getHeading(): ?string
    {
        $user = auth()->user();
        return $user->hasRole('hr_liaison') ? 'Grievances Assigned' : 'Users Registered';
    }

    protected function getData(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate) : now()->subDays(6);
        $end   = $this->endDate ? Carbon::parse($this->endDate) : now();

        $user = auth()->user();

        if ($user->hasRole('hr_liaison')) {
            $query = Grievance::query()
                ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
                ->whereHas('assignments', function ($q) use ($user) {
                    $q->where('hr_liaison_id', $user->id);
                })
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date');
        } else {
            $query = User::query()
                ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date');
        }

        $records = $query->pluck('total', 'date');
        $period  = CarbonPeriod::create($start, $end);

        $labels = [];
        $data   = [];
        foreach ($period as $date) {
            $labels[] = $date->format('M d');
            $data[]   = $records[$date->format('Y-m-d')] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $user->hasRole('hr_liaison') ? 'Grievances Assigned' : 'Users Registered',
                    'data'  => $data,
                    'backgroundColor' => 'rgba(37, 99, 235, 0.7)',
                    'borderColor'     => 'rgba(37, 99, 235, 1)',
                    'borderWidth'     => 2,
                    'barPercentage'   => 1,
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
