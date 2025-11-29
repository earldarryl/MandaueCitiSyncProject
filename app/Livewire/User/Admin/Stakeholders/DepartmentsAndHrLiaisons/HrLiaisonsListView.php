<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Department;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
#[Title(content: 'HR Liaisons List View')]
class HrLiaisonsListView extends Component
{
    public ?int $departmentId = null;
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public $hrLiaisons = [];
    public $department;

    public $editLiaison = [
        'id' => null,
        'name' => '',
        'email' => '',
        'password' => '',
    ];

    public function mount($department)
    {
        $this->departmentId = $department;
        $this->department = Department::findOrFail($department);

        $this->loadHrLiaisons();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->loadHrLiaisons();
    }

    public function loadHrLiaisons()
    {
        $this->hrLiaisons = User::role('hr_liaison')
            ->when($this->departmentId, fn($query) =>
                $query->whereHas('departments', fn($q) =>
                    $q->where('hr_liaison_departments.department_id', $this->departmentId)
                )
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
            ->when($this->sortField !== 'status', function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->get();
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
            $user->password = $this->editLiaison['password'];
        }

        $user->save();

        $this->loadHrLiaisons();

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
        if (!$this->departmentId) return;

        $user = User::find($userId);

        if ($user) {
            $user->departments()->detach($this->departmentId);
            $this->loadHrLiaisons();

            Notification::make()
                ->title('HR Liaison Removed')
                ->body("{$user->name} has been removed from this department.")
                ->success()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.user.admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view', [
            'department' => $this->department,
            'hrLiaisons' => $this->hrLiaisons,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
