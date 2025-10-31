<?php

namespace App\Livewire\User\HrLiaison\ReportsAndAnalytics;

use Livewire\Component;
use App\Models\Grievance;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Reports & Analytics')]
class Index extends Component
{
    public $startDate;
    public $endDate;
    public $filterType = null;
    public $filterCategory = null;
    public $category = 'all';
    public $categories = [];

    // Data
    public $data = [];

    // Sorting
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');

        $this->categories = Grievance::query()
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', Auth::id()))
            ->distinct()
            ->pluck('grievance_category')
            ->toArray();

        $this->loadData();
    }

    public function applyFilters()
    {
        $this->loadData();

    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadData();
    }

    public function loadData()
    {
        $userId = Auth::id();

        $query = Grievance::query()
            ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $userId))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate));

        if (!empty($this->filterType)) {
            $query->where('grievance_type', $this->filterType);
        }

        if (!empty($this->filterCategory)) {
            $query->where('grievance_category', $this->filterCategory);
        }

        if ($this->category !== 'all') {
            $query->where('grievance_category', $this->category);
        }

        $this->data = $query->orderBy($this->sortField, $this->sortDirection)->get();
    }

    public function exportPDF()
    {
          $pdf = Pdf::loadView('pdf.grievance-report', [
            'data' => $this->data,
            'user' => Auth::user(),
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'filterType' => $this->filterType,
            'filterCategory' => $this->filterCategory,
            'hrName' => Auth::user()->name,
        ]);

        return response()->streamDownload(fn() => print($pdf->output()), 'grievance-report.pdf');
    }

    public function exportCSV()
    {
        $filename = 'grievance-report-' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['Ticket ID', 'Title', 'Type', 'Category', 'Status', 'Processing Days', 'Date']);

        foreach ($this->data as $item) {
            fputcsv($handle, [
                $item->grievance_ticket_id,
                $item->grievance_title,
                $item->grievance_type ?? '-',
                $item->grievance_category,
                $item->grievance_status,
                $item->processing_days ?? '—',
                $item->created_at->format('Y-m-d')
            ]);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return Response::streamDownload(fn() => print($contents), $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.reports-and-analytics.index', [
            'categories' => $this->categories,
        ]);
    }
}
