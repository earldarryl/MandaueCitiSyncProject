<?php

namespace App\Livewire\PrintFiles;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.prints')]
#[Title('Print Reports and Analytics')]
class PrintAdminReports extends Component
{
    public $data;
    public $stats;
    public $filterType;
    public $filterCategory;
    public $startDate;
    public $endDate;
    public $adminName;
    public $dynamicTitle;

    public function mount($key)
    {
        $report = cache()->get($key);

        if (!$report) {
            abort(404, 'Report not found or expired.');
        }

        $this->data = collect(json_decode($report['data']));
        $this->stats = collect($report['stats'] ?? [])->map(fn($s) => (object) $s);
        $this->filterType = $report['filterType'] ?? 'All';
        $this->filterCategory = $report['filterCategory'] ?? 'All Categories';
        $this->startDate = $report['startDate'] ?? null;
        $this->endDate = $report['endDate'] ?? null;
        $this->adminName = $report['adminName'] ?? auth()->user()->name;
        $this->dynamicTitle = $report['dynamicTitle'] ?? '';
    }

    public function render()
    {
        return view('livewire.print-files.print-admin-reports');
    }
}
