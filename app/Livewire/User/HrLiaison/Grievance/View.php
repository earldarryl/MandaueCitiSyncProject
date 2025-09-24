<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('View Grievance')]
class View extends Component
{
    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.view');
    }
}
