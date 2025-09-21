<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\Grievance;
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

     public function goToGrievanceCreate()
    {
        return $this->redirect(
        route('grievance.create', absolute: false),
        navigate: true
        );
    }
    public function goToGrievanceEdit($id)
    {
        return $this->redirect(
        route('grievance.edit', $id, absolute: false),
        navigate: true
        );
    }

    public function deleteGrievance($grievanceId)
    {
        $grievance = Grievance::findOrFail($grievanceId);
        $grievance->delete();

        session()->flash('message', 'Grievance deleted successfully.');

        $this->dispatch('close-all-modals');
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.index', [
            'grievances' => Grievance::with([
                'departments' => fn($query) => $query->distinct(),
                'attachments',
                'user',
            ])
            ->latest()
            ->paginate($this->perPage),
        ]);
    }
}
