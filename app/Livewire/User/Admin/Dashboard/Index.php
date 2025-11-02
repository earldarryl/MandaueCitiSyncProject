<?php

namespace App\Livewire\User\Admin\Dashboard;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\Select;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Index extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $startDate;
    public $endDate;
    public $user;
    public $userModel;

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->userModel = User::class;

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');


        if (session()->pull('just_logged_in', false)) {
            Notification::make()
                ->title('Welcome back, ' . $this->user->name . ' 👋')
                ->body('Good to see you again! Here’s your dashboard.')
                ->success()
                ->send();
        }
    }

    public function applyDates($start, $end): void
    {
        $this->startDate = $start;
        $this->endDate = $end;

        $this->dispatch('dateRangeUpdated', $start, $end);
    }

    public function updatedStartDate($value): void
    {
        $this->dispatch('dateRangeUpdated', $value, $this->endDate);
    }

    public function updatedEndDate($value): void
    {
        $this->dispatch('dateRangeUpdated', $this->startDate, $value);
    }

    public function render()
    {
        return view('livewire.user.admin.dashboard.index', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
