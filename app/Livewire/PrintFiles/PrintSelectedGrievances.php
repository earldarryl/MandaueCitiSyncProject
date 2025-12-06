<?php

namespace App\Livewire\PrintFiles;

use App\Models\Grievance;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.prints')]
#[Title('Print Selected Reports')]
class PrintSelectedGrievances extends Component
{
    public $selectedIds = [];
    public $grievances;
    public $hr_liaison;
    public $admin;

    public function mount($selected = [])
    {
        $user = Auth::user();
        $this->selectedIds = is_array($selected)
            ? $selected
            : array_filter(explode(',', $selected));

        if ($user->hasRole('admin')) {
            $this->admin = $user;

            $this->grievances = Grievance::with(['user', 'attachments', 'departments'])
                ->whereIn('grievance_id', $this->selectedIds)
                ->latest()
                ->get();
        } else {
            $this->hr_liaison = $user;

            $this->grievances = Grievance::with(['user', 'attachments', 'assignments.department'])
                ->whereIn('grievance_id', $this->selectedIds)
                ->whereHas('assignments', function ($q) use ($user) {
                    $q->where('hr_liaison_id', $user->id);
                })
                ->latest()
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.print-files.print-selected-grievances');
    }
}
