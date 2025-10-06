<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\Grievance;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Grievance Reports')]
class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public $user;
    public $perPage = 4;
    public $search = '';
    public $searchInput = '';
    public $filterPriority = '';
    public $filterStatus = '';
    public $filterType = '';
    public $filterDate = '';
    public $totalGrievances = 0;
    public $highPriorityCount = 0;
    public $normalPriorityCount = 0;
    public $lowPriorityCount = 0;

    public $pendingCount = 0;
    public $inProgressCount = 0;
    public $resolvedCount = 0;
    public $closedCount = 0;
    public array $selectedGrievances = [];

    protected $updatesQueryString = ['search'];
    protected $listeners = [
        'poll' => '$refresh',
    ];

    public function mount()
    {
        $this->user = auth()->user();

        if (session()->pull('just_logged_in', false)) {
            Notification::make()
                ->title('Welcome back, ' . $this->user->name . ' 👋')
                ->body('Good to see you again! Here’s your dashboard.')
                ->success()
                ->send();
        }

        if (session()->has('notification')) {
            $notif = session('notification');

            Notification::make()
                ->title($notif['title'])
                ->body($notif['body'])
                ->{$notif['type']}()
                ->send();
        }

        $this->updateStats();

    }

    public function deleteGrievance($grievanceId)
    {
        Grievance::where('id', $grievanceId)->delete();

        session()->flash('message', 'Grievance deleted successfully.');
        $this->dispatch('close-all-modals');
    }

    public function bulkDelete()
    {
        Grievance::whereIn('grievance_id', $this->selectedGrievances)->delete();
        $this->selectedGrievances = [];
        Notification::make()
            ->title('Deleted')
            ->body('Selected grievances deleted successfully.')
            ->success()
            ->send();
    }

    public function bulkMarkHigh()
    {
        Grievance::whereIn('grievance_id', $this->selectedGrievances)
            ->update(['priority_level' => 'High']);

        $this->selectedGrievances = [];
        Notification::make()
            ->title('Updated')
            ->body('Selected grievances marked as High Priority.')
            ->success()
            ->send();
    }

    public function applySearch()
    {
        $this->search = $this->searchInput;
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->searchInput = '';
        $this->search = '';
        $this->resetPage();
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateStats()
    {
        $query = Grievance::where('user_id', auth()->id());

        if ($this->filterPriority) {
            $query->where('priority_level', $this->filterPriority);
        }

        if ($this->filterStatus) {
            $map = [
                'Pending'     => 'pending',
                'In Progress' => 'in_progress',
                'Resolved'    => 'resolved',
                'Closed'      => 'closed',
            ];

            if (isset($map[$this->filterStatus])) {
                $query->where('grievance_status', $map[$this->filterStatus]);
            }
        }

        if ($this->filterType) {
            $query->where('grievance_type', $this->filterType);
        }

        if ($this->filterDate) {
            switch ($this->filterDate) {
                case 'Today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'Yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'This Week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'This Month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'This Year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        $this->totalGrievances   = $query->count();
        $this->highPriorityCount = (clone $query)->where('priority_level', 'High')->count();
        $this->normalPriorityCount = (clone $query)->where('priority_level', 'Normal')->count();
        $this->lowPriorityCount  = (clone $query)->where('priority_level', 'Low')->count();

        $this->pendingCount      = (clone $query)->where('grievance_status', 'pending')->count();
        $this->inProgressCount   = (clone $query)->where('grievance_status', 'in_progress')->count();
        $this->resolvedCount     = (clone $query)->where('grievance_status', 'resolved')->count();
        $this->closedCount       = (clone $query)->where('grievance_status', 'closed')->count();
    }

    public function updated($property)
    {
        if (in_array($property, ['filterPriority', 'filterStatus', 'filterType', 'filterDate'])) {
            $this->updateStats();
        }
    }

    public function render()
    {
        $grievances = Grievance::with([
            'departments' => fn($query) => $query->distinct(),
            'attachments',
            'user',
        ])
        ->where('user_id', auth()->id())
        ->when($this->filterPriority, fn($q) => $q->where('priority_level', $this->filterPriority))
        ->when($this->filterStatus, function ($q) {
            $map = [
                'Pending'     => 'pending',
                'In Progress' => 'in_progress',
                'Resolved'    => 'resolved',
                'Closed'      => 'closed',
            ];

            if (isset($map[$this->filterStatus])) {
                $q->where('grievance_status', $map[$this->filterStatus]);
            }
        })
        ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
        ->where(function($query) {
            $query->where('grievance_title', 'like', '%'.$this->search.'%')
                ->orWhere('grievance_details', 'like', '%'.$this->search.'%')
                ->orWhere('priority_level', 'like', '%'.$this->search.'%')
                ->orWhere('grievance_status', 'like', '%'.$this->search.'%')
                ->orWhere('is_anonymous', 'like', '%'.$this->search.'%');
        })
        ->when($this->filterDate, function ($q) {
            switch ($this->filterDate) {
                case 'Today':
                    $q->whereDate('created_at', now()->toDateString());
                    break;

                case 'Yesterday':
                    $q->whereDate('created_at', now()->subDay()->toDateString());
                    break;

                case 'This Week':
                    $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;

                case 'This Month':
                    $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                    break;

                case 'This Year':
                    $q->whereYear('created_at', now()->year);
                    break;
            }
        })
        ->latest()
        ->paginate($this->perPage);

        return view('livewire.user.citizen.grievance.index', compact('grievances'));
    }
}
