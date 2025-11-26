<?php

namespace App\Livewire\PrintFiles;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.prints')]
#[Title('Print HR Liaison Reports')]
class PrintHrLiaisonReports extends Component
{
    public $data;
    public $statuses;
    public $filterType;
    public $filterCategory;
    public $startDate;
    public $endDate;
    public $hrName;
    public $user;
    public $dynamicTitle;

    public function mount($key)
    {
        $report = cache()->get($key);

        if (!$report) {
            abort(404, 'Report not found or expired.');
        }

        $this->data = collect($report['data'] ?? []);
        $this->statuses = collect($report['statuses'] ?? []);
        $this->user = $report['user'] ??  Auth::user();
        $this->filterType = $report['filterType'] ?? 'All Types';
        $this->filterCategory = $report['filterCategory'] ?? 'All Categories';
        $this->startDate = $report['startDate'] ?? null;
        $this->endDate = $report['endDate'] ?? null;
        $this->hrName = $report['hrName'] ?? auth()->user()->name;
        $this->dynamicTitle = $report['dynamicTitle'] ?? '';
    }

    public function render()
    {
        return view('livewire.print-files.print-hr-liaison-reports');
    }
}
