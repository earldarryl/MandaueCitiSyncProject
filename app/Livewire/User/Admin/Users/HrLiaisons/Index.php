<?php

namespace App\Livewire\User\Admin\Users\HrLiaisons;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('Users | HR Liaisons')]
class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // 🔎 Filters
    public $search = '';
    public $status = 'all'; // online / away / offline / all
    public $role = 'all';

    // ↕ Sorting
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // 📑 Pagination
    public $perPage = 6;

    // Reset pagination when filters/search/sorting change
    public function updating($field)
    {
        if (in_array($field, ['search', 'status', 'role', 'sortField', 'sortDirection'])) {
            $this->resetPage();
        }
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
        $query = User::query();

        // 🔍 Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('contact', 'like', '%' . $this->search . '%');
            });
        }

        // 📂 Status filter
        if ($this->status !== 'all') {
            if ($this->status === 'online') {
                $query->where('last_seen_at', '>=', now()->subMinutes(5));
            } elseif ($this->status === 'away') {
                $query->whereNotNull('last_seen_at')
                      ->where('last_seen_at', '<', now()->subMinutes(5));
            } elseif ($this->status === 'offline') {
                $query->whereNull('last_seen_at');
            }
        }

        // 👥 Role filter
        if ($this->role !== 'all') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });

        }

        // ↕ Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.user.admin.users.hr-liaisons.index', [
            'users' => $query->paginate($this->perPage),
            'roles' => Role::pluck('name')->toArray(),
        ]);
    }
}
