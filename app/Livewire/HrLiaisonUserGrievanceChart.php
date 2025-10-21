<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Js;

class HrLiaisonUserGrievanceChart extends ChartWidget
{
    protected ?string $heading = 'Grievances Assigned (Known vs Anonymous)';
    protected static ?int $sort = 2;

    public $startDate;
    public $endDate;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $hrLiaisonId = auth()->id();

        $grievanceCounts = Grievance::query()
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $hrLiaisonId))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->selectRaw("
                CASE
                    WHEN is_anonymous = 1 THEN 'Anonymous'
                    ELSE 'Known'
                END as submitter_type,
                COUNT(*) as total
            ")
            ->groupBy('submitter_type')
            ->pluck('total', 'submitter_type');

        $labels = ['Known', 'Anonymous'];
        $data = collect($labels)->map(fn($label) => $grievanceCounts[$label] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Grievances Submitted',
                    'data' => $data,
                    'backgroundColor' => [
                        '#2563eb',
                        'rgba(255, 179, 0, 0.8)',
                    ],
                    'borderColor' => [
                        '#2563eb',
                        'rgba(255, 179, 0, 0.8)',
                    ],
                    'borderWidth' => 3,
                    'barThickness' => 80,
                    'maxBarThickness' => 100,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#f9fafb',
                    'bodyColor' => '#e5e7eb',
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                    'cornerRadius' => 10,
                    'padding' => 12,
                    'displayColors' => true,
                    'usePointStyle' => true,
                    'caretPadding' => 8,
                    'caretSize' => 6,
                    'boxPadding' => 6,
                    'titleFont' => [
                        'size' => 15,
                        'weight' => 'bold',
                        'lineHeight' => 1.4,
                    ],
                    'bodyFont' => [
                        'size' => 13,
                        'lineHeight' => 1.3,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'font' => ['size' => 13],
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'font' => ['size' => 13],
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
