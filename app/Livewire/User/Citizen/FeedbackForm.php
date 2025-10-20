<?php

namespace App\Livewire\User\Citizen;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Feedback Form')]
class FeedbackForm extends Component
{
    public function render()
    {
        return view('livewire.user.citizen.feedback-form');
    }
}
