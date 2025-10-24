<?php

namespace App\Livewire\PrintFiles;

use App\Models\Grievance;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.prints')]
#[Title('Print All Grievances')]
class PrintAllGrievances extends Component
{
    public $grievances;
    public $hr_liaison;

    public function mount()
    {
        $this->hr_liaison = Auth::user();

        $this->grievances = Grievance::with(['user', 'attachments', 'assignments.department'])
            ->whereHas('assignments', function ($q) {
                $q->where('hr_liaison_id', $this->hr_liaison->id);
            })
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.print-files.print-all-grievances');
    }
}
