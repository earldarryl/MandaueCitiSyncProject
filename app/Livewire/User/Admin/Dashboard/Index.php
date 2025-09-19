<?php

namespace App\Livewire\User\Admin\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Grievance;
use App\Models\Assignment;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Index extends Component
{
    public $users = [];
    public $user;
    public $userModel;

    public $startDate;
    public $endDate;

    public function updatedStartDate($value)
    {
        $this->calculateStats();
        $this->dispatch('dateRangeUpdated', $value, $this->endDate);
    }

    public function updatedEndDate($value)
    {
        $this->calculateStats();
        $this->dispatch('dateRangeUpdated', $this->startDate, $value);
    }

    protected function calculateStats(): void
    {
        $start = $this->startDate . ' 00:00:00';
        $end = $this->endDate . ' 23:59:59';

        $this->totalUsers = User::whereBetween('created_at', [$start, $end])->count();
        $this->totalGrievances = Grievance::whereBetween('created_at', [$start, $end])->count();
        $this->totalAssignments = Assignment::whereBetween('created_at', [$start, $end])->count();

        $this->onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();

        $this->pendingGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'pending')
            ->count();

        $this->rejectedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'rejected')
            ->count();

        $this->inProgressGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'in progress')
            ->count();

        $this->resolvedGrievances = Grievance::whereBetween('created_at', [$start, $end])
            ->where('grievance_status', 'resolved')
            ->count();
    }
    public function mount()
    {
        $this->user = auth()->user();

        if (Gate::allows('viewAny', User::class)) {
            $this->users = User::all();
            $this->userModel = User::class;
        }

        // optional: initialize dates
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        if (session()->pull('just_logged_in', false)) {
            Notification::make()
                ->title('Welcome back, ' . $this->user->name . ' 👋')
                ->body('Good to see you again! Here’s your dashboard.')
                ->success()
                ->send();

            $this->dispatch('notification-created');
        }
    }

    public function render()
    {
        return view('livewire.user.admin.dashboard.index', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}

