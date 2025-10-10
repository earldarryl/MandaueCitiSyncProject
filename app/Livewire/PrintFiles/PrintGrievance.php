<?php

namespace App\Livewire\PrintFiles;

use App\Models\Grievance;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.prints')]
#[Title('Print Grievance Page')]
class PrintGrievance extends Component
{
    public $grievance;

    public function mount($id)
    {
        $this->grievance = Grievance::with(['user', 'assignments.department'])->findOrFail($id);
    }
    public function render()
    {
        return view('livewire.print-files.print-grievance');
    }
}
