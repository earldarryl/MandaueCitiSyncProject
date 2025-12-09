<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use App\Models\Grievance;
use App\Models\EditRequest;
use App\Models\User;
use Filament\Notifications\Notification;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Request;
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
    public function deleteReport()
    {
        $grievance = $this->grievance;

        if (! $grievance) {
            Notification::make()
                ->title('Error')
                ->body('Report not found or already deleted.')
                ->danger()
                ->send();
            return;
        }

        $title = $grievance->grievance_title;

        $department = $grievance->departments->first();
        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) =>
                $q->where('hr_liaison_departments.department_id', $department?->department_id)
            )->get();

        foreach ($hrLiaisons as $hr) {
            $hr->notify(new GeneralNotification(
                'Report Deleted',
                "A report titled '{$title}' has been deleted.",
                'danger',
                ['grievance_ticket_id' => $grievance->grievance_ticket_id]
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Report Deleted',
                "A report titled '{$title}' has been deleted from the system.",
                'warning',
                ['grievance_ticket_id' => $grievance->grievance_ticket_id]
            ));
        }

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'role_id'      => auth()->user()->roles->first()->id ?? null,
            'module'       => 'Grievances',
            'action'       => 'delete',
            'action_type'  => 'single_delete',
            'model_type'   => Grievance::class,
            'model_id'     => $grievance->grievance_id,
            'description'  => "Grievance '{$title}' deleted by user.",
            'changes'      => null,
            'status'       => 'success',
            'ip_address'   => Request::ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => request()->userAgent(),
            'platform'     => php_uname(),
            'location'     => null,
            'timestamp'    => now(),
        ]);

        $grievance->delete();

        Notification::make()
            ->title('Report Removed')
            ->body("Report '{$title}' has been deleted successfully.")
            ->success()
            ->send();

        $this->dispatch('close-modal-delete');
        $this->redirectRoute('citizen.grievance.index', navigate: true);
    }

    public function editRequest()
    {
        $grievance = $this->grievance;
        $user = auth()->user();

        $existing = EditRequest::where('grievance_id', $grievance->grievance_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            Notification::make()
                ->title('Request Not Allowed')
                ->body('You already have submitted an edit request for this report.')
                ->warning()
                ->send();
            return;
        }

        $editRequest = EditRequest::create([
            'grievance_id' => $grievance->grievance_id,
            'user_id'      => $user->id,
            'status'       => 'pending',
            'reason'       => 'User requested edit permission.',
        ]);

        $requesterName = $grievance->is_anonymous ? 'An anonymous user' : $user->name;

        $assignedHrIds = $grievance->assignments->pluck('hr_liaison_id')->unique();
        $hrLiaisons = User::whereIn('id', $assignedHrIds)->get();

        foreach ($hrLiaisons as $hr) {
            $hr->notify(new GeneralNotification(
                'Edit Request Pending',
                "{$requesterName} requested permission to edit report '{$grievance->grievance_title}'.",
                'info',
                [
                    'grievance_ticket_id' => $grievance->grievance_ticket_id,
                    'edit_request_id'     => $editRequest->id
                ]
            ));
        }

        Notification::make()
            ->title('Request Sent')
            ->body('Your edit request has been sent to the assigned HR Liaisons.')
            ->success()
            ->send();

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Edit Request',
            'action'       => 'create',
            'action_type'  => 'request_edit',
            'model_type'   => EditRequest::class,
            'model_id'     => $editRequest->id,
            'description'  => "{$requesterName} requested edit permission for report '{$grievance->grievance_ticket_id}'.",
            'changes'      => null,
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('device') ?? null,
            'user_agent'   => request()->userAgent(),
            'platform'     => php_uname('s'),
            'location'     => null,
            'timestamp'    => now(),
        ]);
    }
    public function render()
    {
        return view('livewire.user.citizen.grievance.view');
    }
}
