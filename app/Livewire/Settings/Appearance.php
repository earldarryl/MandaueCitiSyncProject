<?php

namespace App\Livewire\Settings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Appearance extends Component
{
    public function render()
    {
        return view('livewire.settings.appearance');
    }
}
