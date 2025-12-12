<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Notifications\GeneralNotification;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('HR Liaisons List View')]
class HrLiaisonsListView extends Component
{
    use WithPagination;

    public Department $department;
    public int $departmentId;
    public ?string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 5;
    protected $listeners = ['refresh' => '$refresh'];
    public $editLiaison = [
        'id' => null,
        'name' => '',
        'email' => '',
        'password' => '',
    ];

    protected $updatesQueryString = ['sortField', 'sortDirection', 'page'];


    public function resetFields(): void
    {
        $this->newLiaison = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $this->resetErrorBag();
    }

    public function mount(Department $department)
    {
        $this->department = $department;
        $this->departmentId = $department->department_id;
    }

    public function loadHrLiaisons()
    {
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

        $this->resetPage();
    }

    public function editHrLiaisonModal($userId)
    {
        $user = User::findOrFail($userId);

        $this->editLiaison = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'password' => '',
        ];
    }

    public function updateHrLiaison($hrLiaisonId)
    {
        $user = User::findOrFail($hrLiaisonId);

        $this->validate(
    [
                'editLiaison.name'     => 'required|string|max:255',
                'editLiaison.email'    => 'required|email|unique:users,email,' . $user->id,
                'editLiaison.password' => 'nullable|string|min:6',
            ],
            [
                'editLiaison.name.required' => 'Please enter the HR Liaison’s full name.',
                'editLiaison.name.string'   => 'The name must contain valid text characters.',
                'editLiaison.name.max'      => 'The name may not exceed 255 characters.',

                'editLiaison.email.required' => 'Please provide an email address.',
                'editLiaison.email.email'    => 'Please enter a valid email address.',
                'editLiaison.email.unique'   => 'This email is already in use by another user.',

                'editLiaison.password.string' => 'The password must be valid text.',
                'editLiaison.password.min'    => 'The password must be at least 6 characters long.',
            ]
        );

        $creator = auth()->user();

        $oldName = $user->name;

        $user->name = $this->editLiaison['name'];
        $user->email = $this->editLiaison['email'];

        if (!empty($this->editLiaison['password'])) {
            $user->password = Hash::make($this->editLiaison['password']);
        }

        if (!$user->isDirty()) {

            $this->dispatch('notify', [
                'type'    => 'warning',
                'title'   => 'No Changes Detected',
                'message' => "No updates were made to HR Liaison <b>{$oldName}</b>.",
            ]);

            return;
        }

        $oldData = $user->getOriginal();
        $user->save();

        $changes = $user->getChanges();

        $changesList = implode(', ', array_map(function($key, $value) use ($oldData) {
            $oldValue = isset($oldData[$key]) ? $oldData[$key] : 'N/A';
            return "{$key}: {$oldValue} → {$value}";
        }, array_keys($changes), $changes));


        $user->notify(new GeneralNotification(
            'Your Profile Was Updated',
            "HR Liaison <b>{$creator->name}</b> updated your profile. Changes: {$changesList}",
            'info',
            [],
            ['type' => 'info'],
            true
        ));

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'HR Liaison Updated',
                "{$creator->name} updated HR Liaison <b>{$user->name}</b>. Changes: {$changesList}",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'HR Liaison Updated Successfully',
            "You successfully updated the HR Liaison <b>{$user->name}</b>.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $otherLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) =>
                $q->where('hr_liaison_departments.department_id', $this->departmentId)
            )
            ->where('id', '!=', $user->id)
            ->get();

        foreach ($otherLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'HR Liaison Updated',
                "{$creator->name} updated HR Liaison <b>{$user->name}</b> in your department.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        ActivityLog::create([
            'user_id'     => $creator->id,
            'role_id'     => $creator->roles->first()?->id,
            'module'      => 'HR Liaisons',
            'action'      => 'Update',
            'action_type' => 'update',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => "Updated HR Liaison <b>{$user->name}</b>. Changes: {$changesList}",
            'changes'     => $changes,
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);

        $this->editLiaison = [
            'id' => null,
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');
        $this->resetFields();
    }

    public function removeLiaison(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return;
        }

        $creator = auth()->user();
        $department = Department::find($this->departmentId);

        $departmentName = $department->department_name;
        $liaisonName = $user->name;

        $user->departments()->detach($this->departmentId);

        $user->notify(new GeneralNotification(
            'Removed From Department',
            "You have been removed from the <b>{$departmentName}</b> department.",
            'warning',
            [],
            ['type' => 'warning'],
            true
        ));

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'HR Liaison Removed',
                "{$creator->name} removed <b>{$liaisonName}</b> from the <b>{$departmentName}</b> department.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'HR Liaison Removed Successfully',
            "You removed <b>{$liaisonName}</b> from the <b>{$departmentName}</b> department.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $otherLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) =>
                $q->where('hr_liaison_departments.department_id', $this->departmentId)
            )
            ->where('id', '!=', $user->id)
            ->get();

        foreach ($otherLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'HR Liaison Removed',
                "{$creator->name} removed <b>{$liaisonName}</b> from the <b>{$departmentName}</b> department.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        ActivityLog::create([
            'user_id'     => $creator->id,
            'role_id'     => $creator->roles->first()?->id,
            'module'      => 'HR Liaisons',
            'action'      => 'Remove',
            'action_type' => 'delete',
            'model_type'  => User::class,
            'model_id'    => $user->id,
            'description' => "Removed HR Liaison <b>{$liaisonName}</b> from <b>{$departmentName}</b> department",
            'changes'     => ['department_id' => $this->departmentId],
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);

        $this->dispatch('refresh');
        $this->dispatch('close-all-modals');
        $this->resetFields();
    }

    public function render()
    {
        $totalAssignments = Assignment::where('department_id', $this->departmentId)
            ->whereHas('grievance', fn($q) => $q->whereNull('deleted_at'))
            ->distinct('grievance_id')
            ->count('grievance_id');


        $hrLiaisons = User::role('hr_liaison')
            ->whereHas('departments', fn($q) =>
                $q->where('hr_liaison_departments.department_id', $this->departmentId)
            )
            ->when($this->sortField === 'status', function ($query) {
                $query->orderByRaw("
                    CASE
                        WHEN last_seen_at IS NULL THEN 3
                        WHEN last_seen_at > NOW() - INTERVAL 5 MINUTE THEN 1
                        ELSE 2
                    END " . ($this->sortDirection === 'asc' ? 'ASC' : 'DESC')
                );
            })
            ->when($this->sortField !== 'status', fn($q) =>
                $q->orderBy($this->sortField, $this->sortDirection)
            )
            ->withCount(['assignments as assigned_count' => fn($q) =>
                $q->where('department_id', $this->departmentId)
                ->whereHas('grievance', fn($g) => $g->whereNull('deleted_at'))
                ->distinct('grievance_id')
            ])
            ->paginate($this->perPage);

        $hrLiaisons->getCollection()->transform(function ($liaison) use ($totalAssignments) {
            $liaison->total_assignments = $totalAssignments;
            return $liaison;
        });

        return view('livewire.user.admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view', [
            'hrLiaisons' => $hrLiaisons,
            'department' => $this->department,
        ]);
    }

}
