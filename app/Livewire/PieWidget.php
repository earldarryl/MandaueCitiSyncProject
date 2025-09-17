<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use App\Models\Grievance;

class PieWidget extends ChartWidget
{
    protected ?string $heading = 'Grievance Status Overview';

    protected function getData(): array
    {
        // Count grievances by status
        $pending = Grievance::where('grievance_status', 'pending')->count();
        $rejected = Grievance::where('grievance_status', 'rejected')->count();
        $inProgress = Grievance::where('grievance_status', 'in_progress')->count();
        $resolved = Grievance::where('grievance_status', 'resolved')->count();

        return [
            'labels' => ['Pending', 'Rejected', 'In Progress', 'Resolved'],
            'datasets' => [
                [
                    'label' => 'Grievances',
                    'data' => [$pending, $rejected, $inProgress, $resolved],
                    'backgroundColor' => [
                        'rgba(253, 224, 71, 0.7)', // Yellow - Pending
                        'rgba(239, 68, 68, 0.7)',   // Red - Rejected
                        'rgba(59, 130, 246, 0.7)',  // Blue - In Progress
                        'rgba(16, 185, 129, 0.7)',  // Green - Resolved
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
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
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
