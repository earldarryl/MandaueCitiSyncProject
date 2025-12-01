<?php

namespace App\Livewire\PrintFiles;

use App\Models\Grievance;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.prints')]
#[Title('Print All Reports')]
class PrintAllGrievances extends Component
{
    public $grievances;
    public $hr_liaison;
    public $admin;

    public function mount()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $this->admin = $user;

            $this->grievances = Grievance::with(['user', 'attachments', 'departments'])
                ->latest()
                ->get();
        } else {
            $this->hr_liaison = $user;

            $this->grievances = Grievance::with(['user', 'attachments', 'assignments.department'])
                ->whereHas('assignments', function ($q) use ($user) {
                    $q->where('hr_liaison_id', $user->id);
                })
                ->latest()
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.print-files.print-all-grievances');
    }
}
