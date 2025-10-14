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
    public $selected = [];
    public $selectAll = false;
    public $totalGrievances = 0;
    public $highPriorityCount = 0;
    public $normalPriorityCount = 0;
    public $lowPriorityCount = 0;
    public $pendingCount = 0;
    public $inProgressCount = 0;
    public $resolvedCount = 0;
    public $closedCount = 0;

    protected $updatesQueryString = [
        'search' => ['except' => ''],
        'filterPriority' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterDate' => ['except' => ''],
    ];

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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPriority() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterType() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }


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

    public function deleteGrievance($grievanceId)
    {
        $grievance = Grievance::find($grievanceId);

        if (! $grievance) {
            Notification::make()
                ->title('Error')
                ->body('Grievance not found or already deleted.')
                ->danger()
                ->send();
            return;
        }

        $title = $grievance->grievance_title;
        $grievance->delete();

        $this->updateStats();
        $this->dispatch('$refresh');
        $this->dispatch('close-delete-modal-'.$grievanceId);

        Notification::make()
            ->title('Grievance Deleted')
            ->body("{$title} was deleted successfully.")
            ->success()
            ->send();
    }


    public function updatedSelectAll($value)
    {
        if ($value) {
            $grievances = Grievance::where('user_id', auth()->id())->latest()->get();
            $this->selected = $grievances->pluck('grievance_id')->toArray();
        } else {
            $this->selected = [];
        }
    }

     public function deleteSelected()
    {
        if (empty($this->selected)) return;

        $grievances = Grievance::whereIn('grievance_id', $this->selected)->get();

        foreach ($grievances as $grievance) {
            $grievance->delete();
        }

        $this->selected = [];
        $this->selectAll = false;
        $this->updateStats();

        Notification::make()
            ->title('Bulk Delete')
            ->body('Selected grievances were deleted successfully.')
            ->success()
            ->send();
    }

    public function markSelectedHighPriority()
    {
        if (empty($this->selected)) return;

        Grievance::whereIn('grievance_id', $this->selected)
            ->update(['priority_level' => 'High']);

        $this->selected = [];
        $this->selectAll = false;
        $this->updateStats();

        Notification::make()
            ->title('Bulk Update')
            ->body('Selected grievances were marked as High Priority.')
            ->success()
            ->send();
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
            $query->when(isset($map[$this->filterStatus]), fn($q) => $q->where('grievance_status', $map[$this->filterStatus]));
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

        $this->totalGrievances     = $query->count();
        $this->highPriorityCount   = (clone $query)->where('priority_level', 'High')->count();
        $this->normalPriorityCount = (clone $query)->where('priority_level', 'Normal')->count();
        $this->lowPriorityCount    = (clone $query)->where('priority_level', 'Low')->count();

        $this->pendingCount      = (clone $query)->where('grievance_status', 'pending')->count();
        $this->inProgressCount   = (clone $query)->where('grievance_status', 'in_progress')->count();
        $this->resolvedCount     = (clone $query)->where('grievance_status', 'resolved')->count();
        $this->closedCount       = (clone $query)->where('grievance_status', 'closed')->count();
    }

    public function render()
    {
        $grievances = Grievance::with(['departments' => fn($q) => $q->distinct(), 'attachments', 'user'])
            ->where('user_id', auth()->id())
            ->when($this->filterPriority, fn($q) => $q->where('priority_level', $this->filterPriority))
            ->when($this->filterStatus, function($q) {
                $map = [
                    'Pending'     => 'pending',
                    'In Progress' => 'in_progress',
                    'Resolved'    => 'resolved',
                    'Closed'      => 'closed',
                ];
                if(isset($map[$this->filterStatus])) $q->where('grievance_status', $map[$this->filterStatus]);
            })
            ->when($this->filterType, fn($q) => $q->where('grievance_type', $this->filterType))
            ->where(function($query) {
                $query->where('grievance_title', 'like', '%'.$this->search.'%')
                      ->orWhere('grievance_details', 'like', '%'.$this->search.'%')
                      ->orWhere('priority_level', 'like', '%'.$this->search.'%')
                      ->orWhere('grievance_status', 'like', '%'.$this->search.'%')
                      ->orWhere('is_anonymous', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterDate, function($q){
                switch($this->filterDate){
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
                        $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        break;
                    case 'This Year':
                        $q->whereYear('created_at', now()->year);
                        break;
                }
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.user.citizen.grievance.index', [
            'grievances' => $grievances,
        ]);
    }
}
