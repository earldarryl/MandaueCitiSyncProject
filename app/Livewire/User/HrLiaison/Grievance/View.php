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
        $roleName = ucfirst($user->roles->first()?->name ?? 'User');

        $this->grievance = Grievance::with(['attachments', 'assignments', 'departments', 'user.userInfo'])
            ->whereHas('assignments', function ($query) use ($user) {
                $query->where('hr_liaison_id', $user->id);
            })
            ->findOrFail($id);

        $isAssigned = $this->grievance->assignments->contains('hr_liaison_id', $user->id);
        if (!$isAssigned) {
            abort(403, 'You are not authorized to view this grievance.');
        }

        if ($this->grievance->grievance_status === 'pending') {
            $this->grievance->forceFill([
                'grievance_status' => 'acknowledged',
            ])->save();

            ActivityLog::create([
                'user_id'      => $user->id,
                'role_id'      => $user->roles->first()?->id,
                'module'       => 'Grievance Management',
                'action'       => "Acknowledged grievance #{$this->grievance->grievance_id}",
                'action_type'  => 'acknowledge',
                'model_type'   => 'App\\Models\\Grievance',
                'model_id'     => $this->grievance->grievance_id,
                'description'  => "{$roleName} ({$user->email}) acknowledged grievance #{$this->grievance->grievance_id}.",
                'status'       => 'success',
                'ip_address'   => request()->ip(),
                'device_info'  => request()->header('User-Agent'),
                'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                'platform'     => php_uname('s'),
                'location'     => null,
                'timestamp'    => now(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.view');
    }
}
