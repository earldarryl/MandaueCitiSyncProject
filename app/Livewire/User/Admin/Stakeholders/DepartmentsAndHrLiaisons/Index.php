<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Grievance;
use App\Notifications\GeneralNotification;
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
    public int $perPage = 5;
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

    public $currentDepartmentId;
    public $availableLiaisons = [];
    public $removeLiaisons = [];

    protected $listeners = ['refresh' => '$refresh'];

    public function resetFields(): void
    {
        $this->newDepartment = [
            'department_name' => '',
            'department_code' => '',
            'department_description' => '',
            'is_active' => '',
            'is_available' => '',
        ];

        $this->editingDepartment = [
            'department_name' => '',
            'department_code' => '',
            'department_description' => '',
            'is_active' => '',
            'is_available' => '',
        ];

        $this->newLiaison = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $this->resetErrorBag();
    }

    public function mount()
    {
        $this->calculateSummary();

        $this->form->fill([
            'create_department_profile' => $this->create_department_profile,
            'create_department_background' => $this->create_department_background,
        ]);
    }

    public function loadAvailableLiaisons($departmentId)
    {
        $this->currentDepartmentId = $departmentId;

        $this->availableLiaisons = User::role('hr_liaison')
            ->whereDoesntHave('departments', fn($query) => $query->where('hr_liaison_departments.department_id', $departmentId))
            ->pluck('name', 'id')
            ->mapWithKeys(fn($name, $id) => [(string)$id => $name])
            ->toArray();
    }

    public function loadRemoveLiaisons($departmentId)
    {
        $this->currentDepartmentId = $departmentId;

        $department = Department::find($departmentId);

        $this->removeLiaisons = $department->hrLiaisons
            ->pluck('name', 'id')
            ->mapWithKeys(fn($name, $id) => [(string)$id => $name])
            ->toArray();
    }

    public function createHrLiaison()
    {
        $this->validate(
    [
                'newLiaison.name'     => 'required|string|max:255',
                'newLiaison.email'    => 'required|email|unique:users,email',
                'newLiaison.password' => 'required|string|min:6',
            ],
            [
                'newLiaison.name.required'     => 'Please enter the name of the HR liaison.',
                'newLiaison.name.string'       => 'Name must be a valid text.',
                'newLiaison.name.max'          => 'Name cannot exceed 255 characters.',

                'newLiaison.email.required'    => 'Please enter an email address.',
                'newLiaison.email.email'       => 'Please provide a valid email address.',
                'newLiaison.email.unique'      => 'This email is already registered. Please choose another one.',

                'newLiaison.password.required' => 'Please enter a password.',
                'newLiaison.password.string'   => 'Password must be a valid string.',
                'newLiaison.password.min'      => 'Password must be at least 6 characters long.',
            ]
        );

        $creator = auth()->user();

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
        $this->dispatch('close-all-modals');
        $this->resetFields();

        $user->notify(new GeneralNotification(
            'Welcome to the HR Liaison Team',
            "You have been registered as an HR Liaison in the system.",
            'success',
            [],
            ['type' => 'success'],
            true,
            [[
                'label' => 'Go to Dashboard',
                'url' => route('hr-liaison.dashboard'),
                'open_new_tab' => false,
            ]]
        ));

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'New HR Liaison Added',
                "{$user->name} has been added as an HR Liaison.",
                'info',
                [],
                ['type' => 'info'],
                true,
                [[
                'label' => 'View Departments & HR Liaisons',
                'url' => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                'open_new_tab' => false,
                ]]
            ));
        }

        $creator->notify(new GeneralNotification(
            'HR Liaison Created Successfully',
            "You added {$user->name} as a new HR Liaison.",
            'success',
            [],
            ['type' => 'success'],
            true,
            [[
                'label' => 'View Departments & HR Liaisons',
                'url' => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                'open_new_tab' => false,
            ]]
        ));

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'role_id'     => auth()->user()->roles->first()?->id,
            'module'      => 'HR Liaisons',
            'action'      => 'Create',
            'action_type' => 'create',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => "Created new HR Liaison named '{$user->name}'",
            'changes'     => $user->toArray(),
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);

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
        $this->validate(
            [
                'newDepartment.department_name'        => 'required|string|max:255|unique:departments,department_name',
                'newDepartment.department_code'        => 'required|string|max:50|unique:departments,department_code',
                'newDepartment.department_description' => 'nullable|string|max:1000',
                'newDepartment.is_active'              => 'required',
                'newDepartment.is_available'           => 'required',
            ],
            [
                'newDepartment.department_name.required' => 'Please enter a department name.',
                'newDepartment.department_name.string'   => 'Department name must be valid text.',
                'newDepartment.department_name.max'      => 'Department name cannot exceed 255 characters.',
                'newDepartment.department_name.unique'   => 'This department name already exists.',

                'newDepartment.department_code.required' => 'Please enter a department code.',
                'newDepartment.department_code.string'   => 'Department code must be valid text.',
                'newDepartment.department_code.max'      => 'Department code cannot exceed 50 characters.',
                'newDepartment.department_code.unique'   => 'This department code is already in use.',

                'newDepartment.department_description.string' => 'Description must be valid text.',
                'newDepartment.department_description.max'    => 'Description cannot exceed 1000 characters.',

                'newDepartment.is_active.required'    => 'Please select whether the department is active.',
                'newDepartment.is_available.required' => 'Please select whether the department is available.',
            ]
        );

        $create_department_profile = null;
        $create_department_background = null;

        if (!empty($this->create_department_profile)) {
            $create_department_profile = $this->create_department_profile->store('departments/profile', 'public');
        }

        if (!empty($this->create_department_background)) {
            $create_department_background = $this->create_department_background->store('departments/backgrounds', 'public');
        }

        $isActiveValue    = strtolower($this->newDepartment['is_active']) === 'active' ? 1 : 0;
        $isAvailableValue = strtolower($this->newDepartment['is_available']) === 'yes' ? 1 : 0;

        $department = Department::create([
            'department_name'        => $this->newDepartment['department_name'],
            'department_code'        => $this->newDepartment['department_code'],
            'department_description' => $this->newDepartment['department_description'],
            'is_active'              => $isActiveValue,
            'is_available'           => $isAvailableValue,
            'department_profile'     => $create_department_profile,
            'department_bg'          => $create_department_background,
        ]);

        $this->newDepartment = [
            'department_name'             => '',
            'department_code'             => '',
            'department_description'      => '',
            'is_active'                   => '',
            'is_available'                => '',
            'create_department_profile'   => null,
            'create_department_background' => null,
        ];

        $this->create_department_profile = null;
        $this->create_department_background = null;

        $this->calculateSummary();
        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');
        $this->resetFields();

        $creator = auth()->user();

        $creator->notify(new GeneralNotification(
            'Department Created Successfully',
            "You created the new department <b>{$department->department_name}</b>.",
            'success',
            [],
            ['type' => 'success'],
            true,
            [[
                'label'        => 'View Departments',
                'url'          => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                'open_new_tab' => false,
            ]]
        ));

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'New Department Created',
                "{$creator->name} has created the department <b>{$department->department_name}</b>.",
                'info',
                [],
                ['type' => 'info'],
                true,
                [[
                    'label'        => 'View Departments',
                    'url'          => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                    'open_new_tab' => false,
                ]]
            ));
        }

        ActivityLog::create([
            'user_id'     => $creator->id,
            'role_id'     => $creator->roles->first()?->id ?? null,
            'module'      => 'Department',
            'action'      => 'Create',
            'action_type' => 'create',
            'model_type'  => Department::class,
            'model_id'    => $department->department_id,
            'description' => "Created a new department named '{$department->department_name}'",
            'changes'     => $department->toArray(),
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);
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
            'editingDepartment.department_name'        => 'required|string|max:255',
            'editingDepartment.department_code'        => 'required|string|max:50',
            'editingDepartment.department_description' => 'nullable|string|max:1000',
            'editingDepartment.is_active'              => 'required',
            'editingDepartment.is_available'           => 'required',
        ]);

        $department = Department::find($this->editingDepartment['department_id']);
        if (!$department) {
            $this->dispatch('notify', [
                'type'    => 'warning',
                'title'   => 'Department Not Found',
                'message' => 'The selected department could not be found.',
            ]);
            return;
        }

        $originalData = $department->toArray();

        $newProfilePath = null;
        $newBackgroundPath = null;

        if (!empty($this->edit_department_profile)) {
            $newProfilePath = $this->edit_department_profile->store('departments/profile', 'public');
        }

        if (!empty($this->edit_department_background)) {
            $newBackgroundPath = $this->edit_department_background->store('departments/backgrounds', 'public');
        }

        $isActiveValue    = strtolower($this->editingDepartment['is_active']) === 'active' ? 1 : 0;
        $isAvailableValue = strtolower($this->editingDepartment['is_available']) === 'yes' ? 1 : 0;

        $department->fill([
            'department_name'        => $this->editingDepartment['department_name'],
            'department_code'        => $this->editingDepartment['department_code'],
            'department_description' => $this->editingDepartment['department_description'],
            'is_active'              => $isActiveValue,
            'is_available'           => $isAvailableValue,
        ]);

        if ($newProfilePath) {
            $department->department_profile = $newProfilePath;
        }
        if ($newBackgroundPath) {
            $department->department_bg = $newBackgroundPath;
        }

        if (!$department->isDirty()) {
            $this->dispatch('notify', [
                'type'    => 'warning',
                'title'   => 'No Changes Detected',
                'message' => "No updates were made to <b>{$department->department_name}</b>.",
            ]);
            return;
        }

        $department->save();

        $this->editingDepartment = [
            'department_id'            => null,
            'department_name'          => '',
            'department_code'          => '',
            'department_description'   => '',
            'is_active'                => '',
            'is_available'             => '',
            'edit_department_profile'  => null,
            'edit_department_background' => null,
        ];
        $this->edit_department_profile = null;
        $this->edit_department_background = null;

        $this->calculateSummary();
        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');
        $this->resetFields();

        $currentUser = auth()->user();

        $currentUser->notify(new GeneralNotification(
            'Department Updated',
            "You have successfully updated the department <b>{$department->department_name}</b>.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $department->department_id))
            ->get();

        foreach ($hrLiaisons as $hr) {
            $hr->notify(new GeneralNotification(
                'Department Updated',
                "The department <b>{$department->department_name}</b> has been updated.",
                'info',
                [],
                ['type' => 'info'],
                true,
                [[
                    'label' => 'View Department',
                    'url'   => route('hr-liaison.department.view', $department->department_id),
                    'open_new_tab' => false
                ]]
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $currentUser->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Department Updated',
                "The department <b>{$department->department_name}</b> has been updated by {$currentUser->name}.",
                'info',
                [],
                ['type' => 'info'],
                true,
                [[
                    'label' => 'View Departments',
                    'url'   => route('admin.stakeholders.departments-and-hr-liaisons.index'),
                    'open_new_tab' => false
                ]]
            ));
        }


        ActivityLog::create([
            'user_id'     => auth()->id(),
            'role_id'     => auth()->user()->roles->first()?->id,
            'module'      => 'Departments',
            'action'      => 'Update',
            'action_type' => 'update',
            'model_type'  => Department::class,
            'model_id'    => $department->department_id,
            'description' => "Updated department '{$department->department_name}'",
            'changes'     => [
                'before' => $originalData,
                'after'  => $department->toArray()
            ],
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);
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
            $this->dispatch('notify', [
                'type'    => 'warning',
                'title'   => 'No HR Liaisons Selected',
                'message' => 'Please select at least one HR Liaison to add.',
            ]);
            return;
        }

        $department->hrLiaisons()->attach($this->selectedLiaisonsToAdd);

        $creator = auth()->user();

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

        $existingLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $departmentId))
            ->whereNotIn('id', $this->selectedLiaisonsToAdd)
            ->get();

        foreach ($existingLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'New HR Liaisons Added',
                "{$creator->name} added new HR Liaisons to <b>{$department->department_name}</b>.",
                'info',
                [],
                ['type' => 'info'],
                true,
            ));
        }

        $newLiaisons = User::whereIn('id', $this->selectedLiaisonsToAdd)->get();

        foreach ($newLiaisons as $newLiaison) {
            $newLiaison->notify(new GeneralNotification(
                'You Have Been Added to a Department',
                "You are now assigned as an HR Liaison for <b>{$department->department_name}</b>.",
                'success',
                [],
                ['type' => 'success'],
                true
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'HR Liaisons Added',
                "{$creator->name} added new HR Liaisons to the <b>{$department->department_name}</b> department.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'HR Liaisons Added Successfully',
            "You successfully added HR Liaisons to <b>{$department->department_name}</b>.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $this->reset('selectedLiaisonsToAdd');
        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');

    }


    public function removeLiaison($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        if (empty($this->selectedLiaisonsToRemove)) {

            $this->dispatch('notify', [
                'type'    => 'warning',
                'title'   => 'No HR Liaisons Selected',
                'message' => 'Please select at least one HR Liaison to remove.',
            ]);
            return;
        }

        $creator = auth()->user();

        $removedLiaisons = User::whereIn('id', $this->selectedLiaisonsToRemove)->get();

        $remainingLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $departmentId))
            ->whereNotIn('id', $this->selectedLiaisonsToRemove)
            ->get();

        $department->hrLiaisons()->detach($this->selectedLiaisonsToRemove);

        Assignment::where('department_id', $departmentId)
            ->whereIn('hr_liaison_id', $this->selectedLiaisonsToRemove)
            ->delete();

        foreach ($remainingLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'HR Liaison Removed',
                "{$creator->name} removed one or more HR Liaisons from <b>{$department->department_name}</b>.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        foreach ($removedLiaisons as $removed) {
            $removed->notify(new GeneralNotification(
                'Removed from Department',
                "You have been removed as an HR Liaison from <b>{$department->department_name}</b>.",
                'warning',
                [],
                ['type' => 'warning'],
                true
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'HR Liaison Removal',
                "{$creator->name} removed HR Liaisons from the <b>{$department->department_name}</b> department.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'HR Liaisons Removed Successfully',
            "You removed HR Liaisons from <b>{$department->department_name}</b>.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $this->reset('selectedLiaisonsToRemove');
        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');

    }

    public function deleteDepartment($departmentId)
    {
        $department = Department::with('hrLiaisons')->find($departmentId);

        if (!$department) {
            $this->dispatch('notify', [
                'type'    => 'warning',
                'title'   => 'Department Not Found',
                'message' => 'The selected department could not be found.',
            ]);
            return;
        }

        $departmentName = $department->department_name;

        $creator = auth()->user();

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        $existingLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) =>
                $q->where('hr_liaison_departments.department_id', $departmentId)
            )
            ->get();

        $department->delete();

        $this->calculateSummary();
        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Department Deleted',
                "{$creator->name} deleted the <b>{$departmentName}</b> department.",
                'warning',
                [],
                ['type' => 'warning'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'Department Deleted Successfully',
            "You successfully deleted the <b>{$departmentName}</b> department.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        foreach ($existingLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'Department Deleted',
                "{$creator->name} deleted the <b>{$departmentName}</b> department where you were assigned.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }
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
