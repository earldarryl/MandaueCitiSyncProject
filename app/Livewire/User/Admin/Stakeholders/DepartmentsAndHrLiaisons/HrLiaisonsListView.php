<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
#[Title(content: 'HR Liaisons List View')]
class HrLiaisonsListView extends Component
{
    public ?int $departmentId = null;
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public $hrLiaisons = [];

    public function mount($department)
    {
        $this->departmentId = $department;

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
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
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
            'hrLiaisons' => $this->hrLiaisons,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
