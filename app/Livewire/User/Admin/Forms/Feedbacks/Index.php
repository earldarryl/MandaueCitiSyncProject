<?php

namespace App\Livewire\User\Admin\Forms\Feedbacks;

use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Feedback;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
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
    public string $filterSQD = 'All';
    public string $filterCC = 'All';

    protected $paginationTheme = 'tailwind';

    public function downloadSelectedFeedbacksCsv()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Feedbacks Selected')
                ->body('Please select at least one feedback to download.')
                ->warning()
                ->send();
            return;
        }

        $feedbacks = Feedback::whereIn('id', $this->selected)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="selected_feedbacks_' . now()->format('Y_m_d_His') . '.csv"',
        ];

        $callback = function () use ($feedbacks) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Email', 'Region', 'Gender', 'CC Summary', 'SQD Summary', 'Date Submitted'
            ]);

            foreach ($feedbacks as $feedback) {
                fputcsv($handle, [
                    $feedback->id,
                    $feedback->email,
                    $feedback->region,
                    $feedback->gender,
                    $this->summarizeCC($feedback),
                    $this->summarizeSQD($feedback->answers),
                    $feedback->date->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadAllFeedbacksCsv()
    {
        $feedbacks = $this->allFilteredFeedbacks();

        if ($feedbacks->isEmpty()) {
            Notification::make()
                ->title('No Feedbacks Found')
                ->body('There are no feedbacks available to export.')
                ->warning()
                ->send();
            return;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="all_feedbacks_' . now()->format('Y_m_d_His') . '.csv"',
        ];

        $callback = function () use ($feedbacks) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Email', 'Region', 'Gender', 'CC Summary', 'SQD Summary', 'Date Submitted'
            ]);

            foreach ($feedbacks as $feedback) {
                fputcsv($handle, [
                    $feedback->id,
                    $feedback->email,
                    $feedback->region,
                    $feedback->gender,
                    $this->summarizeCC($feedback),
                    $this->summarizeSQD($feedback->answers),
                    $feedback->date->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printSelectedFeedbacks()
    {
        if (empty($this->selected)) {
            Notification::make()
                ->title('No Feedbacks Selected')
                ->body('Please select at least one feedback to print.')
                ->warning()
                ->send();
            return;
        }

        $feedbacks = Feedback::whereIn('id', $this->selected)->get();

        if ($feedbacks->isEmpty()) {
            Notification::make()
                ->title('No Feedbacks Found')
                ->body('The selected feedbacks were not found.')
                ->warning()
                ->send();
            return;
        }

        return redirect()->route('print-selected-feedbacks', [
            'selected' => implode(',', $feedbacks->pluck('id')->toArray()),
        ]);
    }

    public function printAllFeedbacks()
    {
        $feedbacks = $this->allFilteredFeedbacks();

        if ($feedbacks->isEmpty()) {
            Notification::make()
                ->title('No Feedbacks Found')
                ->body('There are no feedbacks available to print.')
                ->warning()
                ->send();
            return;
        }

        return redirect()->route('print-all-feedbacks', [
            'feedbacks' => $feedbacks->pluck('id')->toArray(),
        ]);
    }


    public function getTotalFeedbacksProperty()
    {
        return $this->allFilteredFeedbacks()->count();
    }

    public function getMostCommonCCProperty()
    {
        $feedbacks = $this->allFilteredFeedbacks();

        $ccFields = ['cc1', 'cc2', 'cc3'];
        $allResponses = $feedbacks->flatMap(function ($feedback) use ($ccFields) {
            return collect($ccFields)->map(fn($f) => $feedback->$f)->filter();
        })->toArray();

        if (empty($allResponses)) return 'N/A';

        $counts = array_count_values($allResponses);
        arsort($counts);
        $top = array_key_first($counts);

        return match ($top) {
            1 => 'Strong Awareness',
            2 => 'Moderate Awareness',
            3 => 'Low Awareness',
            4 => 'No Awareness',
            default => 'N/A',
        };
    }

    public function getMostCommonSQDProperty()
    {
        $feedbacks = $this->allFilteredFeedbacks();

        $allAnswers = $feedbacks->flatMap(fn($f) => is_array($f->answers) ? $f->answers : json_decode($f->answers, true) ?? [])->toArray();

        if (empty($allAnswers)) return 'N/A';

        $counts = array_count_values($allAnswers);
        arsort($counts);
        $top = array_key_first($counts);

        return match ($top) {
            5, 4 => 'Mostly Agree',
            1, 2 => 'Mostly Disagree',
            3 => 'Neutral',
            default => 'N/A',
        };
    }

    public function allFilteredFeedbacks()
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

        $all = $query->get();

        return $all->filter(function ($feedback) {
            $ccSummary = $this->summarizeCC($feedback);
            $sqdSummary = $this->summarizeSQD($feedback->answers);

            if ($this->filterSQD === 'Most Agree' && stripos($sqdSummary, 'expresses satisfaction') === false) return false;
            if ($this->filterSQD === 'Most Disagree' && stripos($sqdSummary, 'expresses dissatisfaction') === false) return false;
            if ($this->filterCC !== 'All' && stripos($ccSummary, $this->filterCC) === false) return false;

            return true;
        });
    }

    public function getFeedbacksProperty()
    {
        $page = request()->get('page', 1);
        $all = $this->allFilteredFeedbacks();

        // Sort
        if (in_array($this->sortField, ['sqd_summary', 'cc_summary'])) {
            $all = $all->sortBy(function ($feedback) {
                if ($this->sortField === 'sqd_summary') {
                    return $this->summarizeSQD($feedback->answers);
                } else {
                    return $this->summarizeCC($feedback);
                }
            }, SORT_REGULAR, $this->sortDirection === 'desc')->values();
        } else {
            $all = $all->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc')->values();
        }

        $perPage = 10;
        $paginated = new LengthAwarePaginator(
            $all->forPage($page, $perPage),
            $all->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginated;
    }

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

    public function applySearch() { $this->resetPage(); }
    public function applyFilters() { $this->resetPage(); }
    public function clearSearch()
    {
        $this->searchInput = '';
        $this->filterSQD = 'All';
        $this->filterCC = 'All';
        $this->resetPage();
    }

    public function summarizeCC($feedback)
    {
        $ccFields = ['cc1', 'cc2', 'cc3'];
        $responses = [];

        foreach ($ccFields as $field) {
            if (isset($feedback->$field)) {
                $responses[] = (int) $feedback->$field;
            }
        }

        if (empty($responses)) return 'No CC responses provided.';

        $counts = array_count_values($responses);

        $categories = [
            'High Awareness' => Arr::get($counts, 1, 0),
            'Medium Awareness' => Arr::get($counts, 2, 0),
            'Low Awareness' => Arr::get($counts, 3, 0),
            'No Awareness' => Arr::get($counts, 4, 0),
            'N/A' => Arr::get($counts, 5, 0),
        ];

        $maxCount = max($categories);
        $dominantCategories = array_keys($categories, $maxCount);

        if (in_array('High Awareness', $dominantCategories)) return 'This feedback shows strong awareness of the CC.';
        if (in_array('Medium Awareness', $dominantCategories)) return 'This feedback shows moderate awareness of the CC.';
        if (in_array('Low Awareness', $dominantCategories)) return 'This feedback shows low awareness of the CC.';
        if (in_array('No Awareness', $dominantCategories)) return 'This feedback shows no awareness of the CC.';

        return 'This feedback has no applicable CC summary.';
    }

    public function summarizeSQD($answers)
    {
        if (!is_array($answers)) $answers = json_decode($answers, true) ?: [];

        if (empty($answers)) return 'No answers provided';

        $counts = array_count_values($answers);

        $categories = [
            'Strongly Disagree' => Arr::get($counts, 1, 0),
            'Disagree' => Arr::get($counts, 2, 0),
            'Neither' => Arr::get($counts, 3, 0),
            'Agree' => Arr::get($counts, 4, 0),
            'Strongly Agree' => Arr::get($counts, 5, 0),
            'N/A' => Arr::get($counts, 6, 0),
        ];

        $maxCount = max($categories);
        $dominantCategories = array_keys($categories, $maxCount);

        if (in_array('Strongly Agree', $dominantCategories) || in_array('Agree', $dominantCategories)) {
            return 'This feedback expresses satisfaction as it is answered mostly ' . implode(' / ', $dominantCategories) . '.';
        }

        if (in_array('Strongly Disagree', $dominantCategories) || in_array('Disagree', $dominantCategories)) {
            return 'This feedback expresses dissatisfaction as it is answered mostly ' . implode(' / ', $dominantCategories) . '.';
        }

        if (in_array('Neither', $dominantCategories)) return 'This feedback expresses neutrality as it is answered mostly Neither.';

        return 'This feedback cannot be summarized.';
    }

    public function render()
    {
        return view('livewire.user.admin.forms.feedbacks.index', [
            'feedbacks' => $this->feedbacks,
            'totalFeedbacks' => $this->totalFeedbacks,
            'mostCommonCC' => $this->mostCommonCC,
            'mostCommonSQD' => $this->mostCommonSQD,
        ]);
    }

}
