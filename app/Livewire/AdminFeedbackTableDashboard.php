<?php

namespace App\Livewire;

use App\Models\Feedback;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class AdminFeedbackTableDashboard extends TableWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;
    protected static ?string $pollingInterval = '10s';

    // ... (summarizeCC and summarizeSQD methods remain the same)

    public function summarizeCC(Feedback $feedback): string
    {
        $ccFields = ['cc1', 'cc2', 'cc3'];
        $responses = [];

        foreach ($ccFields as $field) {
            if (isset($feedback->$field)) {
                $responses[] = (int)$feedback->$field;
            }
        }

        if (empty($responses)) {
            return 'No CC responses';
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
        $dominant = array_keys($categories, $maxCount);

        if (in_array('High Awareness', $dominant)) return 'High Awareness';
        if (in_array('Medium Awareness', $dominant)) return 'Medium Awareness';
        if (in_array('Low Awareness', $dominant)) return 'Low Awareness';
        if (in_array('No Awareness', $dominant)) return 'No Awareness';
        return 'N/A';
    }

    public function summarizeSQD(Feedback $feedback): string
    {
        if (is_string($feedback->answers)) {
            $answers = json_decode($feedback->answers, true) ?: [];
        } elseif (is_array($feedback->answers)) {
            $answers = $feedback->answers;
        } else {
            $answers = [];
        }

        if (empty($answers)) return 'No answers';

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
        $dominant = array_keys($categories, $maxCount);

        if (in_array('Strongly Agree', $dominant) || in_array('Agree', $dominant)) return 'Most Agree';
        if (in_array('Strongly Disagree', $dominant) || in_array('Disagree', $dominant)) return 'Most Disagree';
        if (in_array('Neither', $dominant)) return 'Neutral';
        return 'N/A';
    }

    public function getHeading(): string | Htmlable | null
    {
        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full p-3">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-700 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="size-6 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-3-3v6m9 3V6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3Z" />
                            </svg>
                            <span>Feedback Records Overview</span>
                        </h2>
                    </div>
                </div>

                <div x-data="{ open: false }" class="mt-1">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-sm font-medium text-gray-700 dark:text-gray-200
                            hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <span>About this table</span>
                        <svg xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-180': open }"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse
                        class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-zinc-800
                            rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                        This table displays all <span class="font-semibold text-gray-800 dark:text-gray-300">feedback submissions</span>
                        from users, including their <span class="font-semibold text-gray-800 dark:text-gray-300">gender</span>,
                        <span class="font-semibold text-gray-800 dark:text-gray-300">email</span>, and
                        <span class="font-semibold text-gray-800 dark:text-gray-300">awareness of and satisfaction with services</span>.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To help administrators evaluate service satisfaction, analyze demographic insights,
                        and review user suggestions to improve institutional service delivery.
                    </div>
                </div>
            </div>
        HTML);
    }

    public function table(Table $table): Table
    {
        $widget = $this;

        return $table
            ->query(function () {
                $query = Feedback::query();

                if ($this->startDate && $this->endDate) {
                    $query->whereBetween('created_at', [
                        $this->startDate . ' 00:00:00',
                        $this->endDate . ' 23:59:59',
                    ]);
                }

                return $query;
            })
            ->header($this->getHeading())
            ->columns([
                Tables\Columns\CheckboxColumn::make('selected')->label('')->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Date Submitted')
                    ->dateTime('F d, Y • h:i A')
                    ->sortable()
                    ->alignCenter()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                BadgeColumn::make('gender')
                    ->label('Gender')
                    ->colors([
                        'info' => 'Male',
                        'danger' => 'Female',
                        'gray' => 'Other',
                    ])
                    ->alignCenter()
                    ->extraAttributes(['class' => 'text-sm font-medium'])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('email')
                    ->label('Email')
                    ->alignCenter()
                    ->placeholder('N/A')
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('cc_summary')
                    ->label('CC Summary')
                    ->sortable()
                    ->alignCenter()
                    ->getStateUsing(fn(Feedback $record) => $this->summarizeCC($record))
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('sqd_summary')
                    ->label('SQD Summary')
                    ->sortable()
                    ->alignCenter()
                    ->getStateUsing(fn(Feedback $record) => $this->summarizeSQD($record))
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),
            ])
            ->filters([

                SelectFilter::make('gender')
                    ->label('Gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ]),

                SelectFilter::make('cc_summary')
                    ->label('CC Summary')
                    ->options([
                        'High Awareness' => 'High Awareness',
                        'Medium Awareness' => 'Medium Awareness',
                        'Low Awareness' => 'Low Awareness',
                        'No Awareness' => 'No Awareness',
                        'N/A' => 'N/A',
                    ]),

                SelectFilter::make('sqd_summary')
                    ->label('SQD Summary')
                    ->options([
                        'Most Agree' => 'Most Agree',
                        'Most Disagree' => 'Most Disagree',
                        'Neutral' => 'Neutral',
                        'N/A' => 'N/A',
                    ]),

            ])
            ->filtersTriggerAction(fn (Action $action) => $action->button()->color('info'))
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn (Feedback $record) => route('admin.forms.feedbacks.view', $record))
                    ->extraAttributes([
                        'wire:navigate' => true,
                        'class' => '
                            !inline-flex !items-center !gap-1.5
                            !px-2.5 !py-1.5 !text-xs !font-semibold
                            !rounded-md !border !border-gray-300
                            !bg-gray-50 !text-gray-700
                            hover:!bg-gray-100 hover:!border-gray-400
                            dark:!bg-zinc-700 dark:!text-gray-200 dark:!border-zinc-600
                            dark:hover:!bg-zinc-600 dark:hover:!border-zinc-500
                            !transition-all !duration-200 !shadow-sm !cursor-pointer
                        ',
                    ])
                    ->tooltip('View grievance details')
                    ->button(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50, 'all'])
            ->searchable()
            ->emptyStateHeading('No Feedback Records Found')
            ->emptyStateDescription('There are currently no feedback submissions within the selected date range or filters.')
            ->emptyStateIcon('heroicon-o-face-frown');
    }
}
