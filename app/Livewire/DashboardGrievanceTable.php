<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;
class DashboardGrievanceTable extends TableWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function baseQuery(): Builder
    {
        $user = Auth::user();

        $query = Grievance::query()->with('user');

        if ($user->hasRole('hr_liaison')) {
            $query->whereHas('assignments', function (Builder $q) use ($user) {
                $q->where('hr_liaison_id', $user->id);
            });
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
        }

        return $query;
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
                            <span>Report Records Overview</span>
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
                        This table lists all <span class="font-semibold text-gray-800 dark:text-gray-300">reports</span>
                        assigned to HR liaisons, including both
                        <span class="font-semibold text-gray-800 dark:text-gray-300">identified</span> and
                        <span class="font-semibold text-gray-800 dark:text-gray-300">anonymous</span> submissions.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To provide an overview of report records, their types, priorities, and identities —
                        enabling HR Liaisons to track case progress, monitor communication transparency,
                        and support decision-making in case management.
                    </div>
                </div>
            </div>
        HTML);
    }


    public function table(Table $table): Table
    {
        $query = $this->baseQuery();

        return $table
            ->query($query)
            ->header($this->getHeading())
            ->columns([

                TextColumn::make('grievance_ticket_id')
                    ->label('Ticket ID')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Ticket ID copied!')
                    ->color('info')
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_title')
                    ->label('Title')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match (strtolower($state)) {
                        'pending' => 'Pending',
                        'acknowledged' => 'Acknowledged',
                        'in_progress' => 'In Progress',
                        'escalated' => 'Escalated',
                        'resolved' => 'Resolved',
                        'unresolved' => 'Unresolved',
                        'closed' => 'Closed',
                        'overdue' => 'Overdue',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'acknowledged',
                        'warning' => 'in_progress',
                        'purple' => 'escalated',
                        'success' => 'resolved',
                        'danger' => 'unresolved',
                        'secondary' => 'closed',
                        'danger' => 'overdue',
                    ])
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'danger' => 'Complaint',
                        'success' => 'Request',
                        'info' => 'Inquiry',
                    ])
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                    TextColumn::make('grievance_category')
                        ->label('Category')
                        ->badge()
                        ->colors(['info' ])
                        ->sortable()
                        ->searchable()
                        ->extraAttributes([
                            'class' => 'text-[12px] font-bold text-center',
                        ])
                        ->extraHeaderAttributes([
                            'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                        ]),

                TextColumn::make('priority_level')
                    ->label('Priority')
                    ->badge()
                    ->colors([
                        'success' => 'Low',
                        'info' => 'Normal',
                        'primary' => 'High',
                        'danger' => 'Critical',
                    ])
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('is_anonymous')
                    ->label('Identity')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Anonymous' : 'Identified')
                    ->colors(fn ($record) => $record->is_anonymous ? ['gray'] : ['success'])
                    ->tooltip(fn ($record) => $record->is_anonymous
                        ? 'Submitted Anonymously'
                        : $record->user?->name)
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'N/A' : $state)
                    ->tooltip(fn ($record) => $record->is_anonymous
                        ? 'Anonymous Report'
                        : $record->user?->name)
                    ->alignCenter()
                    ->sortable()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y • h:i A')
                    ->sortable()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold text-center',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),
            ])

            ->filters([
                SelectFilter::make('priority_level')
                    ->label('Priority')
                    ->options([
                        'High' => 'High',
                        'Medium' => 'Normal',
                        'Low' => 'Low',
                        'Critical' => 'Critical',

                    ]),

                SelectFilter::make('grievance_status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'acknowledged' => 'Acknowledged',
                        'in_progress' => 'In Progress',
                        'escalated' => 'Escalated',
                        'resolved' => 'Resolved',
                        'unresolved' => 'Unresolved',
                        'closed' => 'Closed',
                    ]),

                SelectFilter::make('grievance_type')
                    ->label('Type')
                    ->options([
                        'Complaint' => 'Complaint',
                        'Request' => 'Request',
                        'Inquiry' => 'Inquiry',
                    ]),

                SelectFilter::make('is_anonymous')
                    ->label('Anonymous')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!isset($data['value'])) return $query;
                        return $query->where('is_anonymous', $data['value']);
                    }),
            ])
            ->filtersTriggerAction(fn (Action $action) => $action->button()->color('info'))
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn (Grievance $record) => route('hr-liaison.grievance.view', $record))
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
                    ->tooltip('View report')
                    ->button(),
            ])
            ->searchable()
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50, 'all']);
    }
}
