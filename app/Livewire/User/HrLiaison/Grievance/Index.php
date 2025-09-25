<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\Grievance;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Grievance Reports')]
class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 10;
    public $search = '';

    protected $updatesQueryString = ['search'];

    // Polling every 10 seconds
    protected $listeners = [
        'poll' => '$refresh',
    ];

    public function mount()
    {
        if (session()->has('notification')) {
            $notif = session('notification');

            Notification::make()
                ->title($notif['title'])
                ->body($notif['body'])
                ->{$notif['type']}()
                ->send();
        }
    }

    public function goToGrievanceView($id)
    {
        return $this->redirect(route('hr-liaison.grievance.view', $id, absolute: false), navigate: true);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $grievances = Grievance::with([
            'departments' => fn($query) => $query->distinct(),
            'attachments',
            'user',
        ])
        ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
        ->where(function($query) {
            $query->where('grievance_title', 'like', '%'.$this->search.'%')
                  ->orWhere('grievance_details', 'like', '%'.$this->search.'%')
                  ->orWhere('priority_level', 'like', '%'.$this->search.'%')
                  ->orWhere('grievance_status', 'like', '%'.$this->search.'%')
                  ->orWhere('is_anonymous', 'like', '%'.$this->search.'%');
        })
        ->latest()
        ->paginate($this->perPage);

        return view('livewire.user.hr-liaison.grievance.index', compact('grievances'));
    }
}
