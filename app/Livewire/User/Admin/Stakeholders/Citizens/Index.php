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
    public $nameStartsWithInput = '';
    public $nameStartsWith = '';
    public $filterColumnInput = '';
    public $filterColumn = '';
    public $genderFilterInput = '';
    public $statusFilterInput = '';
    public $deactivatedFilterInput = '';
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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function applyFilters()
    {
        $columnMap = [
            'First Name' => 'first_name',
            'Middle Name' => 'middle_name',
            'Last Name' => 'last_name',
            'Email' => 'email',
            'Barangay' => 'barangay',
        ];

        $this->nameStartsWith = $this->nameStartsWithInput;
        $this->filterColumn = $columnMap[$this->filterColumnInput] ?? null;

        $this->resetPage();
    }

    public function render()
    {
        $citizensQuery = User::role('citizen')
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
            ->when($this->genderFilterInput, function ($query) {
                $gender = strtolower($this->genderFilterInput);
                $query->whereHas('userInfo', fn($sub) => $sub->where('gender', $gender));
            })
            ->when($this->statusFilterInput, function ($query) {
                $status = strtolower($this->statusFilterInput);
                $query->where(function ($q) use ($status) {
                    if ($status === 'online') {
                        $q->whereNotNull('last_seen_at')
                        ->where('last_seen_at', '>', now()->subMinutes(5));
                    } elseif ($status === 'away') {
                        $q->whereNotNull('last_seen_at')
                        ->where('last_seen_at', '<=', now()->subMinutes(5));
                    } elseif ($status === 'offline') {
                        $q->whereNull('last_seen_at');
                    }
                });
            })
            ->when($this->deactivatedFilterInput, function ($query) {
                if ($this->deactivatedFilterInput === 'Active') {
                    $query->whereNull('deleted_at');
                } elseif ($this->deactivatedFilterInput === 'Deactivated') {
                    $query->whereNotNull('deleted_at');
                }
            })
            ->when($this->nameStartsWith && $this->filterColumn, function ($query) {
                $column = $this->filterColumn;
                $letter = $this->nameStartsWith;

                if ($column === 'email') {
                    $query->where('email', 'like', $letter . '%');
                } else {
                    $query->whereHas('userInfo', fn($sub) => $sub->where($column, 'like', $letter . '%'));
                }
            })
            ->when(in_array($this->sortField, ['first_name', 'middle_name', 'last_name']), function ($query) {
                $query->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id')
                    ->select('users.*')
                    ->addSelect("user_infos.{$this->sortField} as sort_field")
                    ->orderBy('sort_field', $this->sortDirection);
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            });


        $citizens = $citizensQuery->paginate($this->perPage);

        $allCitizens = User::role('citizen')->with('userInfo')->get();

        $totalCitizens = $allCitizens->count();
        $totalMale     = $allCitizens->filter(fn($user) => optional($user->userInfo)->gender === 'Male')->count();
        $totalFemale   = $allCitizens->filter(fn($user) => optional($user->userInfo)->gender === 'Female')->count();
        $totalOnline   = $allCitizens->where('status', 'online')->count();
        $totalAway     = $allCitizens->where('status', 'away')->count();
        $totalOffline  = $allCitizens->where('status', 'offline')->count();

        return view('livewire.user.admin.stakeholders.citizens.index', [
            'citizens' => $citizens,
            'totalCitizens' => $totalCitizens,
            'totalMale' => $totalMale,
            'totalFemale' => $totalFemale,
            'totalOnline' => $totalOnline,
            'totalAway' => $totalAway,
            'totalOffline' => $totalOffline,
        ]);
    }

}
