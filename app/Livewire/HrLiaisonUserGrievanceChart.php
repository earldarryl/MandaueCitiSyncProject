<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class HrLiaisonUserGrievanceChart extends ChartWidget
{
    protected ?string $heading = 'Total Grievances Submitted';
    protected static ?int $sort = 2;

    // Accept start and end dates as props
    public $startDate;
    public $endDate;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $hrLiaisonId = auth()->id();

        // Count total grievances assigned to this HR liaison
        $totalGrievances = Grievance::query()
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $hrLiaisonId))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Grievances Submitted',
                    'data' => [$totalGrievances],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'barPercentage' => 0.6,
                    'barThickness' => 60,
                ],
            ],
            'labels' => ['Grievances'], // Single label
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'height' => 400,
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Grievances',
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Grievances',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
