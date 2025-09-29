<?php

namespace App\Livewire\User\Citizen\Grievance;

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

    public function goToGrievanceCreate()
    {
        return $this->redirect(route('citizen.grievance.create', absolute: false), navigate: true);
    }
    public function goToGrievanceView($id)
    {
        return $this->redirect(route('citizen.grievance.view', $id, absolute: false), navigate: true);
    }
    public function goToGrievanceEdit($id)
    {
        return $this->redirect(route('citizen.grievance.edit', $id, absolute: false), navigate: true);
    }
    public function deleteGrievance($grievanceId)
    {
        $grievance = Grievance::findOrFail($grievanceId);
        $grievance->delete();

        session()->flash('message', 'Grievance deleted successfully.');
        $this->dispatch('close-all-modals');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $grievances = Grievance::with([
            'departments' => fn($query) => $query->distinct(),
            'attachments',
            'user',
        ])
        ->where('user_id', auth()->id())
        ->where(function($query) {
            $query->where('grievance_title', 'like', '%'.$this->search.'%')
                ->orWhere('grievance_details', 'like', '%'.$this->search.'%')
                ->orWhere('priority_level', 'like', '%'.$this->search.'%')
                ->orWhere('grievance_status', 'like', '%'.$this->search.'%')
                ->orWhere('is_anonymous', 'like', '%'.$this->search.'%');
        })
        ->latest()
        ->paginate($this->perPage);

        return view('livewire.user.citizen.grievance.index', compact('grievances'));
    }
}
