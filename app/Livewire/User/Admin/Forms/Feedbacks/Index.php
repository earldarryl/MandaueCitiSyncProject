<?php

namespace App\Livewire\User\Admin\Forms\Feedbacks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Feedback;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use Illuminate\Support\Arr;

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

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->searchInput = '';
        $this->filterSQD = 'All';
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

        $feedbacks = $query->paginate(10);

        $filtered = $feedbacks->getCollection()->filter(function ($feedback) {
            $ccSummary = $this->summarizeCC($feedback);
            $sqdSummary = $this->summarizeSQD($feedback->answers);

            if ($this->filterSQD === 'Most Agree' && stripos($sqdSummary, 'expresses satisfaction') === false) {
                return false;
            }
            if ($this->filterSQD === 'Most Disagree' && stripos($sqdSummary, 'expresses dissatisfaction') === false) {
                return false;
            }

            if ($this->filterCC !== 'All' && stripos($ccSummary, $this->filterCC) === false) {
                return false;
            }

            return true;
        });

        if (in_array($this->sortField, ['sqd_summary', 'cc_summary'])) {
            $filtered = $filtered->sortBy(function ($feedback) {
                if ($this->sortField === 'sqd_summary') {
                    return $this->summarizeSQD($feedback->answers);
                } else {
                    return $this->summarizeCC($feedback);
                }
            }, SORT_REGULAR, $this->sortDirection === 'desc')->values();
        }

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered,
            $filtered->count(),
            10,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );
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

        if (empty($responses)) {
            return 'No CC responses provided.';
        }

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

        if (in_array('High Awareness', $dominantCategories)) {
            return 'This feedback shows strong awareness of the CC.';
        } elseif (in_array('Medium Awareness', $dominantCategories)) {
            return 'This feedback shows moderate awareness of the CC.';
        } elseif (in_array('Low Awareness', $dominantCategories)) {
            return 'This feedback shows low awareness of the CC.';
        } elseif (in_array('No Awareness', $dominantCategories)) {
            return 'This feedback shows no awareness of the CC.';
        } else {
            return 'This feedback has no applicable CC summary.';
        }
    }

    public function summarizeSQD($answers)
    {
        if (!is_array($answers)) {
            $answers = json_decode($answers, true) ?: [];
        }

        if (empty($answers)) {
            return 'No answers provided';
        }

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
        } elseif (in_array('Strongly Disagree', $dominantCategories) || in_array('Disagree', $dominantCategories)) {
            return 'This feedback expresses dissatisfaction as it is answered mostly ' . implode(' / ', $dominantCategories) . '.';
        } elseif (in_array('Neither', $dominantCategories)) {
            return 'This feedback expresses neutrality as it is answered mostly Neither.';
        } else {
            return 'This feedback cannot be summarized.';
        }
    }

    public function summarizeFeedback($feedback)
    {
        $sqd = $this->summarizeSQD($feedback->answers);
        $cc  = $this->summarizeCC($feedback);

        return trim("{$sqd} {$cc}");
    }

    public function render()
    {
        return view('livewire.user.admin.forms.feedbacks.index', [
            'feedbacks' => $this->feedbacks,
        ]);
    }
}
