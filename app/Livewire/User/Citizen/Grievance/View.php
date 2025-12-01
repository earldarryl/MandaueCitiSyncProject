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
    public $limit = 10;
    public $totalRemarksCount;
    protected $listeners = [
        'loadMore' => 'loadMore',
    ];
    public function mount(Grievance $grievance)
    {
        $user = auth()->user();
        $roleName = ucfirst($user->roles->first()?->name ?? 'User');

        $this->grievance = $grievance->load(['attachments', 'assignments', 'departments', 'user.userInfo']);

        if ($this->grievance->user_id !== $user->id) {
            abort(403, 'You are not authorized to view this report.');
        }

        $this->totalRemarksCount = count($this->grievance->grievance_remarks ?? []);

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

    public function loadMore()
    {
        if ($this->limit < $this->totalRemarksCount) {
            $this->limit += 10;
        }

        $this->dispatch('remarks-updated', canLoadMore: $this->canLoadMore);
    }

    public function getRemarksProperty()
    {
        $remarks = $this->grievance->grievance_remarks ?? [];

        return array_slice($remarks, -$this->limit);
    }

    public function getCanLoadMoreProperty()
    {
        return $this->limit < $this->totalRemarksCount;
    }

    public function readableSize($bytes)
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1024 * 1024 * 1024) return round($bytes / (1024 * 1024), 1) . ' MB';
        return round($bytes / (1024 * 1024 * 1024), 1) . ' GB';
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.view');
    }
}
