<?php

namespace App\Livewire\User\Admin\ReportsAndAnalytics;

use Livewire\Component;
use App\Models\Grievance;
use App\Models\Feedback;
use App\Models\User;
use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Reports & Analytics')]
class Index extends Component
{

    public $filterType = '';
    public $grievanceType;
    public $grievancePriority;
    public $grievanceStatus;
    public $startDate;
    public $endDate;
    public function applyFilters()
    {

    }
    public function render()
    {
        $data = collect();

        switch ($this->filterType) {
            case 'Grievances':
                $data = Grievance::with('user')
                    ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->when($this->grievanceType, fn($q) => $q->where('grievance_type', $this->grievanceType))
                    ->when($this->grievancePriority, fn($q) => $q->where('priority_level', $this->grievancePriority))
                    ->when($this->grievanceStatus, fn($q) => $q->where('grievance_status', $this->grievanceStatus))
                    ->latest()
                    ->get();
                break;

            case 'Feedbacks':
                $data = Feedback::with('user')
                    ->when($this->startDate, fn($q) => $q->whereDate('date', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('date', '<=', $this->endDate))
                    ->latest()
                    ->get();
                break;

            case 'Users':
                $data = User::when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
                    ->latest()
                    ->get();
                break;

            case 'Activity Logs':
                $data = ActivityLog::with('user', 'role')
                    ->when($this->startDate, fn($q) => $q->whereDate('timestamp', '>=', $this->startDate))
                    ->when($this->endDate, fn($q) => $q->whereDate('timestamp', '<=', $this->endDate))
                    ->latest()
                    ->get();
                break;
        }

        return view('livewire.user.admin.reports-and-analytics.index', [
            'data' => $data
        ]);
    }
}
