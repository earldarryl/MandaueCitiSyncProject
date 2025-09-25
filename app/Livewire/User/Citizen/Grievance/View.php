<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\Grievance;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('View Grievance')]
class View extends Component
{
    public Grievance $grievance;

    public function mount($id)
    {
        $this->grievance = Grievance::with('attachments', 'assignments', 'departments')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.view');
    }
}
