<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role; // only if using Spatie Roles

class UserGrid extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';
    public $status = 'all'; // Active / Inactive / All
    public $role = 'all';   // Role filter (if using Spatie Roles)

    // Reset pagination when search or filters change
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }
    public function updatingRole()   { $this->resetPage(); }

    public function render()
    {
        $query = User::query();

        // 🔍 Search by name or email
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // 📂 Filter by status (if `status` column exists on users table)
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // 👥 Filter by role (only works if using Spatie Roles)
        if ($this->role !== 'all') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        return view('livewire.user-grid', [
            'users' => $query->paginate(6), // show 6 per page (2x3 grid)
            'roles' => Role::pluck('name')->toArray(), // roles for dropdown
        ]);
    }
}
