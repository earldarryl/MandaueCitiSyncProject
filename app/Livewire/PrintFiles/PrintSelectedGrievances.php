<?php

namespace App\Livewire\PrintFiles;

use App\Models\Grievance;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.prints')]
#[Title('Print Selected Grievances')]
class PrintSelectedGrievances extends Component
{
    public $selectedIds = [];
    public $grievances;
    public $hr_liaison;

    public function mount($selected = [])
    {
        $this->hr_liaison = Auth::user();
        $this->selectedIds = is_array($selected)
            ? $selected
            : array_filter(explode(',', $selected));


        $this->grievances = Grievance::with(['user', 'attachments', 'assignments.department'])
            ->whereIn('grievance_id', $this->selectedIds)
            ->whereHas('assignments', function ($q) {
                $q->where('hr_liaison_id', $this->hr_liaison->id);
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.print-files.print-selected-grievances');
    }
}
