<?php

namespace App\Livewire\User\Admin\Forms\Grievances;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Grievances')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.admin.forms.grievances.index');
    }
}
