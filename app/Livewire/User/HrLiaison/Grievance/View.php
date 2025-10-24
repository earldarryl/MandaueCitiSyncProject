<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\Grievance;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $user = auth()->user();

        $this->grievance = Grievance::with([
            'attachments',
            'assignments',
            'departments',
            'user.userInfo'
        ])
        ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
        ->findOrFail($id);

    }

    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.view');
    }
}
