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
    public $filterUserType;
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
    $this->filterUserType = $report['filterUserType'] ?? 'Citizen';
    $this->startDate = $report['startDate'] ?? null;
    $this->endDate = $report['endDate'] ?? null;
    $this->adminName = $report['adminName'] ?? auth()->user()->name;
    $this->dynamicTitle = $report['dynamicTitle'] ?? '';

    if ($this->filterType === 'Users') {
        $this->data = $this->data->filter(function ($user) {
            $roles = $user->roles ?? [];

            if (in_array('citizen', $roles)) {
                return !is_null($user->userInfo);
            }

            if (in_array('hr_liaison', $roles)) {
                return false;
            }

            return false;
        })->values();
    }
}


    public function render()
    {
        return view('livewire.print-files.print-admin-reports');
    }
}
