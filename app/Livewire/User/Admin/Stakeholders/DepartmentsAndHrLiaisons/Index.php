<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Department;
use App\Models\User;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
#[Title('Departments & HR Liaisons')]
class Index extends Component
{
    use WithPagination;

    public $sortField = 'department_name';
    public $sortDirection = 'asc';
    public $selectedLiaisonsToAdd = [];
    public $selectedLiaisonsToRemove = [];
    public $searchInput = '';
    public $filterActive = 'All';
    public $filterAvailability = 'All';
    public $filterHRStatus = 'All';
    public $filterDate = 'Show All';
    public $nameStartsWith = 'All';
    public $totalLiaisonHours = 0;
    public $totalDepartments = 0;
    public $recentDepartment = null;
    public $editingDepartment = [
        'department_name' => '',
        'department_code' => '',
        'department_description' => '',
        'is_active' => '',
        'is_available' => '',
    ];

    protected $listeners = ['refresh' => '$refresh'];

    public function mount()
    {
        $this->calculateSummary();
    }

    public function calculateSummary()
    {
        $this->totalDepartments = Department::count();

        $this->recentDepartment = Department::latest('created_at')->first();

        $this->totalLiaisonHours = User::role('hr_liaison')
            ->get()
            ->sum(function($user) {
                if (!$user->last_seen_at) return 0;
                $diffInHours = now()->diffInMinutes($user->last_seen_at) / 60;
                return $diffInHours > 0 ? $diffInHours : 0;
            });
    }

    public function getDepartmentsProperty()
    {
        $query = Department::with('hrLiaisons')->withCount('hrLiaisons');

        if ($this->searchInput) {
            $query->where(function ($q) {
                $q->where('department_name', 'like', '%' . $this->searchInput . '%')
                ->orWhere('department_code', 'like', '%' . $this->searchInput . '%');
            });
        }

        if ($this->filterActive !== 'All') {
            $query->where('is_active', $this->filterActive === 'Active' ? 1 : 0);
        }

        if ($this->filterAvailability !== 'All') {
            $query->where('is_available', $this->filterAvailability === 'Yes' ? 1 : 0);
        }

        if ($this->filterHRStatus !== 'All') {
            $query->whereHas('hrLiaisons', function ($q) {
                if ($this->filterHRStatus === 'Active') {
                    $q->where('is_active', 1)->where('is_available', 1);
                } else {
                    $q->where(function($sub) {
                        $sub->where('is_active', 0)->orWhere('is_available', 0);
                    });
                }
            });
        }

        if ($this->filterDate !== 'Show All') {
            $query->where(function($q) {
                switch ($this->filterDate) {
                    case 'Today':
                        $q->whereDate('created_at', now());
                        break;
                    case 'Yesterday':
                        $q->whereDate('created_at', now()->subDay());
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
            });
        }

        if ($this->nameStartsWith !== 'All') {
            $query->where('department_name', 'like', $this->nameStartsWith . '%');
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function editDepartment($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        $this->editingDepartment = [
            'department_id' => $department->department_id,
            'department_name' => $department->department_name,
            'department_code' => $department->department_code,
            'department_description' => $department->department_description,
            'is_active' => $department->is_active ? 'Active' : 'Inactive',
            'is_available' => $department->is_available ? 'Yes' : 'No',
        ];
    }

    public function updateDepartment()
    {
        $this->validate([
            'editingDepartment.department_name' => 'required|string|max:255',
            'editingDepartment.department_code' => 'required|string|max:50',
            'editingDepartment.department_description' => 'nullable|string|max:1000',
            'editingDepartment.is_active' => 'required',
            'editingDepartment.is_available' => 'required',
        ]);

        $department = Department::find($this->editingDepartment['department_id']);

        if (!$department) {
            Notification::make()
                ->title('Department Not Found')
                ->body('The selected department could not be found.')
                ->danger()
                ->duration(4000)
                ->send();
            return;
        }

        $isActiveValue = $this->editingDepartment['is_active'];
        if (is_string($isActiveValue)) {
            $isActiveValue = match (strtolower($isActiveValue)) {
                'active' => 1,
                'inactive' => 0,
                default => 0,
            };
        }

        $isAvailableValue = $this->editingDepartment['is_available'];
        if (is_string($isAvailableValue)) {
            $isAvailableValue = match (strtolower($isAvailableValue)) {
                'yes' => 1,
                'no' => 0,
                default => 0,
            };
        }

        $department->update([
            'department_name' => $this->editingDepartment['department_name'],
            'department_code' => $this->editingDepartment['department_code'],
            'department_description' => $this->editingDepartment['department_description'],
            'is_active' => (int) $isActiveValue,
            'is_available' => (int) $isAvailableValue,
        ]);

        $this->editingDepartment = [
            'department_id' => null,
            'department_name' => '',
            'department_code' => '',
            'department_description' => '',
            'is_active' => '',
            'is_available' => '',
        ];

        Notification::make()
            ->title('Department Updated')
            ->body("The department <b>{$department->department_name}</b> has been successfully updated.")
            ->success()
            ->duration(4000)
            ->send();
    }

    public function applySearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->searchInput = '';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getAvailableLiaisons($departmentId)
    {
        $department = Department::find($departmentId);
        if (!$department) return [];

        $assignedIds = $department->hrLiaisons->pluck('id')->toArray();

        return User::role('hr_liaison')
            ->whereNotIn('id', $assignedIds)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getAssignedLiaisons($departmentId)
    {
        $department = Department::find($departmentId);
        if (!$department) return [];

        return $department->hrLiaisons->pluck('name', 'id')->toArray();
    }

    public function saveLiaison($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        if (empty($this->selectedLiaisonsToAdd)) {
            Notification::make()
                ->title('No HR Liaisons Selected')
                ->body('Please select at least one HR Liaison to add.')
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }

        $department->hrLiaisons()->attach($this->selectedLiaisonsToAdd);
        $this->reset('selectedLiaisonsToAdd');

        Notification::make()
            ->title('HR Liaisons Added Successfully')
            ->body("Selected HR Liaisons have been added to the {$department->department_name} department.")
            ->success()
            ->duration(4000)
            ->send();
    }

    public function removeLiaison($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        if (empty($this->selectedLiaisonsToRemove)) {
            Notification::make()
                ->title('No HR Liaisons Selected')
                ->body('Please select at least one HR Liaison to remove.')
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }

        $department->hrLiaisons()->detach($this->selectedLiaisonsToRemove);
        $this->reset('selectedLiaisonsToRemove');

        Notification::make()
            ->title('HR Liaisons Removed Successfully')
            ->body("Selected HR Liaisons have been removed from the {$department->department_name} department.")
            ->success()
            ->duration(4000)
            ->send();
    }

    public function render()
    {
        $departments = $this->departments->map(function ($department) {
            $department->availableLiaisons = $this->getAvailableLiaisons($department->id);
            $department->assignedLiaisons = $this->getAssignedLiaisons($department->id);

            $total = $department->hrLiaisons->count();
            $active = $department->hrLiaisons->filter(fn($user) => $user->isOnline())->count();
            $department->hrLiaisonsStatus = "$active / $total";

            return $department;
        });

        return view('livewire.user.admin.stakeholders.departments-and-hr-liaisons.index', [
            'departments' => $departments,
        ]);
    }

}
