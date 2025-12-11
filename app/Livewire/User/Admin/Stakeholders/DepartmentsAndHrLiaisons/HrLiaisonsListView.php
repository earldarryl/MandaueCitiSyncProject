<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use App\Models\Assignment;
use App\Models\Grievance;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Department;
use Filament\Notifications\Notification;
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

    public $editLiaison = [
        'id' => null,
        'name' => '',
        'email' => '',
        'password' => '',
    ];

    protected $updatesQueryString = ['sortField', 'sortDirection', 'page'];


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

        $this->validate([
            'editLiaison.name' => 'required|string|max:255',
            'editLiaison.email' => 'required|email|unique:users,email,' . $user->id,
            'editLiaison.password' => 'nullable|string|min:6',
        ]);

        $user->name = $this->editLiaison['name'];
        $user->email = $this->editLiaison['email'];

        if (!empty($this->editLiaison['password'])) {
            $user->password = Hash::make($this->editLiaison['password']);
        }

        $user->save();

        Notification::make()
            ->title('HR Liaison Updated')
            ->body("{$user->name}'s information has been successfully updated.")
            ->success()
            ->send();

        $this->editLiaison = [
            'id' => null,
            'name' => '',
            'email' => '',
            'password' => '',
        ];
    }

    public function removeLiaison(int $userId)
    {
        $user = User::find($userId);

        if ($user) {
            $user->departments()->detach($this->departmentId);

            Notification::make()
                ->title('HR Liaison Removed')
                ->body("{$user->name} has been removed from this department.")
                ->success()
                ->send();
        }
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

        // Add total assignments for reference
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
