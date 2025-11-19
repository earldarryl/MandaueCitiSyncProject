<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use App\Models\Grievance;
use Illuminate\Support\Facades\Auth;

class GrievancesReportPieChart extends ChartWidget
{
    protected ?string $heading = null;
    protected static ?int $sort = 4;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $filterType = null;
    public ?string $filterCategory = null;

    public function mount($startDate = null, $endDate = null, $filterType = null, $filterCategory = null): void
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? now()->format('Y-m-d');
        $this->filterType = $filterType;
        $this->filterCategory = $filterCategory;
    }

    public function getHeading(): ?string
    {
        return null;
    }

    protected function baseQuery()
    {
        $user = Auth::user();

        return Grievance::query()
            ->when($user->hasRole('hr_liaison'), function ($query) use ($user) {
                $query->whereHas('assignments', function ($q) use ($user) {
                    $q->where('hr_liaison_id', $user->id);
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59',
                ]);
            })
            ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
            ->when($this->filterCategory, fn($q) => $q->where('grievance_category', $this->filterCategory));
    }

    protected function getData(): array
    {
        $now = now();

        $grievances = $this->baseQuery()->get(['grievance_status', 'processing_days', 'created_at']);

        $delayedCount = 0;
        $resolvedCount = 0;
        $pendingCount = 0;

        foreach ($grievances as $g) {
            $daysPassed = $g->created_at->diffInDays($now);

            if ($daysPassed > ($g->processing_days ?? 0)) {
                $delayedCount++;
            } else {
                $status = strtolower($g->grievance_status);
                if ($status === 'resolved') {
                    $resolvedCount++;
                } elseif (in_array($status, ['pending', 'in_progress', 'acknowledged', 'escalated'])) {
                    $pendingCount++;
                }
            }
        }

        $data = [$delayedCount, $resolvedCount, $pendingCount];
        $total = array_sum($data);

        $labels = ['Delayed', 'Resolved', 'Pending'];
        $labelsWithPercent = collect($labels)->map(function ($label, $i) use ($data, $total) {
            $count = $data[$i] ?? 0;
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return "{$label} ({$count} - {$percentage}%)";
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Grievance Status Breakdown',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(251, 191, 36, 0.85)',
                    ],
                    'borderColor' => ['#ef4444', '#10b981', '#fbbf24'],
                ],
            ],
            'labels' => $labelsWithPercent,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

   protected function getOptions(): array
{
    return [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'right',
                'align' => 'center',
                'labels' => [
                    'font' => [
                        'size' => 16,
                        'weight' => '600',
                    ],
                    'boxWidth' => 20,
                    'boxHeight' => 20,
                    'padding' => 20,
                    'usePointStyle' => true,
                    'pointStyle' => 'circle',
                ],
            ],
            'tooltip' => [
                'backgroundColor' => 'rgba(0, 0, 0, 0.95)',
                'bodyColor' => '#e5e7eb',
                'cornerRadius' => 10,
                'padding' => 12,
                'titleFont' => ['size' => 14, 'weight' => 'bold'],
                'bodyFont' => ['size' => 14],
            ],
        ],
        'layout' => [
            'padding' => [
                'top' => 10,
                'bottom' => 10,
                'right' => 10,
                'left' => 10,
            ],
        ],
    ];
}


    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
