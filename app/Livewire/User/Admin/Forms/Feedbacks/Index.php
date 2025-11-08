<?php

namespace App\Livewire\User\Admin\Forms\Feedbacks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Feedback;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Feedbacks')]
class Index extends Component
{
    use WithPagination;

    public string $sortField = 'date';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public bool $selectAll = false;

    public string $searchInput = '';
    public string $filterDate = 'Show All';

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->feedbacks->pluck('id')->toArray() : [];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function applySearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->searchInput = '';
        $this->resetPage();
    }

    public function getFeedbacksProperty()
    {
        $query = Feedback::query();

        if (!empty($this->searchInput)) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . $this->searchInput . '%')
                  ->orWhere('region', 'like', '%' . $this->searchInput . '%')
                  ->orWhere('gender', 'like', '%' . $this->searchInput . '%');
            });
        }

        switch ($this->filterDate) {
            case 'Today':
                $query->whereDate('date', Carbon::today());
                break;
            case 'Yesterday':
                $query->whereDate('date', Carbon::yesterday());
                break;
            case 'This Week':
                $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'This Month':
                $query->whereMonth('date', Carbon::now()->month);
                break;
            case 'This Year':
                $query->whereYear('date', Carbon::now()->year);
                break;
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
    }

    public function render()
    {
        return view('livewire.user.admin.forms.feedbacks.index', [
            'feedbacks' => $this->feedbacks,
        ]);
    }
}
