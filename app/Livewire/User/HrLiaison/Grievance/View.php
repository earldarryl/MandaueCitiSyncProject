<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\ActivityLog;
use App\Models\Grievance;
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

        $this->grievance = Grievance::with(['attachments', 'assignments', 'departments', 'user.userInfo'])
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
            ->findOrFail($id);

        if ($this->grievance->grievance_status === 'pending') {
            $this->grievance->forceFill([
                'grievance_status' => 'acknowledged',
            ])->save();

            ActivityLog::create([
                'user_id'     => $user->id,
                'role_id'     => $user->roles->first()?->id,
                'action'      => "Acknowledged grievance #{$this->grievance->grievance_id}",
                'timestamp'   => now(),
                'ip_address'  => request()->ip(),
                'device_info' => request()->header('User-Agent'),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.view');
    }
}
