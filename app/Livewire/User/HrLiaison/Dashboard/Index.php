<?php

namespace App\Livewire\User\HrLiaison\Dashboard;

use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Index extends Component
{
    public $startDate;
    public $endDate;

    protected $listeners = ['dateRangeUpdated'];

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function dateRangeUpdated($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.dashboard.index', [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}

