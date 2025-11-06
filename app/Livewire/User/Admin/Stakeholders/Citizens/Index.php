<?php

namespace App\Livewire\User\Admin\Stakeholders\Citizens;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;

#[Layout('layouts.app')]
#[Title('Citizens')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'last_name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $searchInput = '';

    public function applySearch()
    {
        $this->search = $this->searchInput;
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->reset(['search', 'searchInput']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortField()
    {
        $this->resetPage();
    }

    public function updatingSortDirection()
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
    }

    public function render()
    {
        $citizens = User::role('citizen')
            ->with('userInfo')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('email', 'like', "%{$this->search}%")
                        ->orWhereHas('userInfo', function ($sub) {
                            $sub->where('first_name', 'like', "%{$this->search}%")
                                ->orWhere('middle_name', 'like', "%{$this->search}%")
                                ->orWhere('last_name', 'like', "%{$this->search}%")
                                ->orWhere('barangay', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when(in_array($this->sortField, ['first_name', 'middle_name', 'last_name']), function ($query) {
                $query->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
                    ->select('users.*') // keep main table fields
                    ->addSelect("user_infos.{$this->sortField} as sort_field")
                    ->orderBy('sort_field', $this->sortDirection);
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate($this->perPage);

        return view('livewire.user.admin.stakeholders.citizens.index', [
            'citizens' => $citizens,
        ]);
    }

}
