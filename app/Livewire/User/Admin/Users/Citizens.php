<?php

namespace App\Livewire\User\Admin\Users;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Citizens')]
class Citizens extends Component
{
    public function render()
    {
        return view('livewire.user.admin.users.citizens');
    }
}
