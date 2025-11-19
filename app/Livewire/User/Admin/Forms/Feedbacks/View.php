<?php

namespace App\Livewire\User\Admin\Forms\Feedbacks;

use Livewire\Component;
use App\Models\Feedback;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('View Feedback')]
class View extends Component
{
    public Feedback $feedback;

    public function mount($id)
    {
        $this->feedback = Feedback::findOrFail($id);

        if (!$this->feedback->answers) {
            $this->feedback->answers = [];
        }
    }

    public function render()
    {
        return view('livewire.user.admin.forms.feedbacks.view');
    }
}
