<?php

namespace App\Livewire\User\Admin\Stakeholders\Citizens;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Citizens')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.admin.stakeholders.citizens.index');
    }
}
