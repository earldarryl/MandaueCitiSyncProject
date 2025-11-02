<?php

namespace App\Livewire\User\Admin\Forms\Feedbacks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Feedbacks')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.user.admin.forms.feedbacks.index');
    }
}
