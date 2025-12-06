<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use App\Models\Assignment;
use App\Models\Grievance;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Department;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
#[Layout('layouts.app')]
#[Title('Departments & HR Liaisons')]
class Index extends Component implements Forms\Contracts\HasForms
{
    use WithFileUploads, InteractsWithForms, InteractsWithActions, WithPagination;

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
    public int $perPage = 2;
    public $totalHrLiaisons = 0;
    public $totalLiaisonHours = 0;
    public $totalDepartments = 0;
    public $assignedHrLiaisons = 0;
    public $unassignedHrLiaisons = 0;
    public $recentDepartment = null;
    public $create_department_profile;
    public $create_department_background;
    public $edit_department_profile;
    public $edit_department_background;
    public $profilePreview;
    public $backgroundPreview;
    public $newDepartment = [
        'department_name' => '',
        'department_code' => '',
        'department_description' => '',
        'is_active' => '',
        'is_available' => '',
    ];

    public $newLiaison = [
        'name' => '',
        'email' => '',
        'password' => '',
    ];

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

        $this->form->fill([
            'create_department_profile' => $this->create_department_profile,
            'create_department_background' => $this->create_department_background,
        ]);
    }

    public function createHrLiaison()
    {
        $this->validate([
            'newLiaison.name' => 'required|string|max:255',
            'newLiaison.email' => 'required|email|unique:users,email',
            'newLiaison.password' => 'required|string|min:6',
        ]);

        $user = new User([
            'name' => $this->newLiaison['name'],
            'email' => $this->newLiaison['email'],
            'password' => $this->newLiaison['password'],
        ]);

        $user->forceFill([
            'email_verified_at' => $user->freshTimestamp(),
        ])->save();

        $user->assignRole('hr_liaison');

        $this->newLiaison = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $this->calculateSummary();
        $this->dispatch('refresh');

        Notification::make()
            ->title('HR Liaison Added')
            ->body("{$user->name} has been successfully added as HR Liaison.")
            ->success()
            ->send();
    }

    public function calculateSummary()
    {
        $this->totalDepartments = Department::count();
        $this->recentDepartment = Department::latest('created_at')->first();

        $this->totalHrLiaisons = User::role('hr_liaison')->count();

        $this->assignedHrLiaisons = User::role('hr_liaison')
            ->whereHas('departments')
            ->count();

        $this->unassignedHrLiaisons = User::role('hr_liaison')
            ->doesntHave('departments')
            ->count();

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

        return $query->paginate($this->perPage);
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function createDepartment()
    {
        $this->validate([
            'newDepartment.department_name' => 'required|string|max:255|unique:departments,department_name',
            'newDepartment.department_code' => 'required|string|max:50|unique:departments,department_code',
            'newDepartment.department_description' => 'nullable|string|max:1000',
            'newDepartment.is_active' => 'required',
            'newDepartment.is_available' => 'required',
        ]);

        $state = $this->form->getState();
        $create_department_profile = $state['create_department_profile'] ?? null;
        $create_department_background = $state['create_department_background'] ?? null;

        if ($create_department_profile instanceof \Livewire\TemporaryUploadedFile) {
            $create_department_profile = $create_department_profile->store('departments/profile', 'public');
        }

        if ($create_department_background instanceof \Livewire\TemporaryUploadedFile) {
            $create_department_background = $create_department_background->store('departments/backgrounds', 'public');
        }

        $isActiveValue = strtolower($this->newDepartment['is_active']) === 'active' ? 1 : 0;
        $isAvailableValue = strtolower($this->newDepartment['is_available']) === 'yes' ? 1 : 0;

        $department = Department::create([
            'department_name' => $this->newDepartment['department_name'],
            'department_code' => $this->newDepartment['department_code'],
            'department_description' => $this->newDepartment['department_description'],
            'is_active' => $isActiveValue,
            'is_available' => $isAvailableValue,
            'department_profile' => $create_department_profile,
            'department_bg' => $create_department_background,
        ]);

        $this->newDepartment = [
            'department_name' => '',
            'department_code' => '',
            'department_description' => '',
            'is_active' => '',
            'is_available' => '',
            'create_department_profile' => null,
            'create_department_background' => null,
        ];

        $this->calculateSummary();
        $this->dispatch('refresh');

        Notification::make()
            ->title('Department Created')
            ->body("The department <b>{$department->department_name}</b> has been successfully created.")
            ->success()
            ->duration(4000)
            ->send();
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

        $this->profilePreview = $department->department_profile
            ? Storage::url($department->department_profile)
            : null;

        $this->backgroundPreview = $department->department_bg
            ? Storage::url($department->department_bg)
            : null;

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

        $state = $this->form->getState();
        $edit_department_profile = $state['edit_department_profile'] ?? null;
        $edit_department_background = $state['edit_department_background'] ?? null;

        if ($edit_department_profile instanceof \Livewire\TemporaryUploadedFile) {
            $edit_department_profile = $edit_department_profile->store('departments/profile', 'public');
        }

        if ($edit_department_background instanceof \Livewire\TemporaryUploadedFile) {
            $edit_department_background = $edit_department_background->store('departments/backgrounds', 'public');
        }

        $isActiveValue = strtolower($this->editingDepartment['is_active']) === 'active' ? 1 : 0;
        $isAvailableValue = strtolower($this->editingDepartment['is_available']) === 'yes' ? 1 : 0;

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

        $department->update([
            'department_name' => $this->editingDepartment['department_name'],
            'department_code' => $this->editingDepartment['department_code'],
            'department_description' => $this->editingDepartment['department_description'],
            'is_active' => $isActiveValue,
            'is_available' => $isAvailableValue,
            'department_profile' => $edit_department_profile,
            'department_bg' => $edit_department_background,
        ]);

        $this->editingDepartment = [
            'department_id' => null,
            'department_name' => '',
            'department_code' => '',
            'department_description' => '',
            'is_active' => '',
            'is_available' => '',
            'edit_department_profile' => null,
            'edit_department_background' => null,
        ];

        $this->calculateSummary();
        $this->dispatch('refresh');

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

        $grievances = Grievance::whereHas('assignments', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->get();


        foreach ($this->selectedLiaisonsToAdd as $liaisonId) {
            foreach ($grievances as $grievance) {
                Assignment::firstOrCreate([
                    'grievance_id'  => $grievance->grievance_id,
                    'department_id' => $departmentId,
                    'hr_liaison_id' => $liaisonId,
                ], [
                    'assigned_at' => now(),
                ]);
            }
        }

        $this->reset('selectedLiaisonsToAdd');

        Notification::make()
            ->title('HR Liaisons Added & Auto-Assigned')
            ->body("Selected HR Liaisons have been added and automatically assigned to this department's grievances.")
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

        Assignment::where('department_id', $departmentId)
            ->whereIn('hr_liaison_id', $this->selectedLiaisonsToRemove)
            ->delete();

        $this->reset('selectedLiaisonsToRemove');

        Notification::make()
            ->title('HR Liaisons Removed Successfully')
            ->body("Selected HR Liaisons have been removed from the {$department->department_name} department and unassigned from its grievances.")
            ->success()
            ->duration(4000)
            ->send();
    }

    public function deleteDepartment($departmentId)
    {
        $department = Department::find($departmentId);

        if (!$department) {
            Notification::make()
                ->title('Department Not Found')
                ->body('The selected department could not be found.')
                ->danger()
                ->duration(4000)
                ->send();
            return;
        }

        $departmentName = $department->department_name;
        $department->delete();

        $this->calculateSummary();
        $this->dispatch('refresh');

        Notification::make()
            ->title('Department Deleted')
            ->body("The department <b>{$departmentName}</b> has been successfully deleted.")
            ->success()
            ->duration(4000)
            ->send();
    }

    public function render()
    {
        $departments = $this->departments->through(function ($department) {
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
