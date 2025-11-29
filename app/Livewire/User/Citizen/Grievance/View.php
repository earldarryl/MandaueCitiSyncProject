<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use App\Models\Grievance;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('View Report')]

class View extends Component
{
    public Grievance $grievance;
    public function mount(Grievance $grievance)
    {
        $user = auth()->user();
        $roleName = ucfirst($user->roles->first()?->name ?? 'User');

        $this->grievance = $grievance->load(['attachments', 'assignments', 'departments', 'user.userInfo']);

        if ($this->grievance->user_id !== $user->id) {
            abort(403, 'You are not authorized to view this report.');
        }

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Management',
            'action'       => "Viewed report #{$this->grievance->grievance_ticket_id}",
            'action_type'  => 'view',
            'model_type'   => Grievance::class,
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "{$roleName} ({$user->name}) viewed report #{$this->grievance->grievance_ticket_id} ({$this->grievance->grievance_title}).",
            'changes'      => [],
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'location'     => geoip(request()->ip())?->city,
            'timestamp'    => now(),
        ]);

    }

    public function refreshGrievance()
    {
        $this->dispatch('$refresh');
        $this->dispatch('refreshChat', grievanceId: $this->grievance->grievance_id);
        $this->grievance->refresh();
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.view');
    }
}
