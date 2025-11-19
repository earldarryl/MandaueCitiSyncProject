<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Confirm Password')]
class ConfirmPassword extends Component
{
    public function render()
    {
        return view('livewire.pages.auth.confirm-password');
    }
}
