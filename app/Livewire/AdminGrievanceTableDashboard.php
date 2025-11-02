<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;
class AdminGrievanceTableDashboard extends TableWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;
    protected static ?string $pollingInterval = '10s';
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
                            <span>Grievance Records Overview</span>
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
                        This table lists all <span class="font-semibold text-gray-800 dark:text-gray-300">grievances</span>
                        submitted by employees or citizens. It includes details such as the
                        <span class="font-semibold text-gray-800 dark:text-gray-300">status</span>,
                        <span class="font-semibold text-gray-800 dark:text-gray-300">type</span>,
                        <span class="font-semibold text-gray-800 dark:text-gray-300">priority</span>, and
                        <span class="font-semibold text-gray-800 dark:text-gray-300">assigned department</span>.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To enable HR liaisons and administrators to monitor grievances,
                        assess workflow progress, and support transparency and accountability in case management.
                    </div>
                </div>
            </div>
        HTML);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Grievance::query()->with(['user', 'departments']);

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
                TextColumn::make('grievance_ticket_id')
                    ->label('TICKET ID')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Ticket ID copied!')
                    ->weight('semibold')
                    ->color('primary')
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_title')
                    ->label('TITLE')
                    ->sortable()
                    ->searchable()
                    ->weight('semibold')
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_status')
                    ->label('STATUS')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'acknowledged',
                        'warning' => 'in_progress',
                        'purple' => 'escalated',
                        'success' => 'resolved',
                        'danger' => 'unresolved',
                        'secondary' => 'closed',
                    ])
                    ->searchable()
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->weight('semibold')
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_type')
                    ->label('TYPE')
                    ->badge()
                    ->colors([
                        'danger' => 'Complaint',
                        'info' => 'Request',
                        'success' => 'Inquiry',
                    ])
                    ->weight('semibold')
                    ->searchable()
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('priority_level')
                    ->label('PRIORITY')
                    ->badge()
                    ->colors([
                        'success' => 'Low',
                        'info' => 'Normal',
                        'danger' => 'High',
                    ])
                    ->weight('semibold')
                    ->searchable()
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('departments.department_name')
                    ->label('DEPARTMENT')
                    ->badge()
                    ->colors(['primary'])
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->weight('semibold')
                    ->searchable()
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('user.name')
                    ->label('SUBMITTED BY')
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Anonymous' : $state)
                    ->tooltip(fn ($record) => $record->is_anonymous
                        ? 'Anonymous Grievance'
                        : $record->user?->email)
                    ->weight('semibold')
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('created_at')
                    ->label('CREATED ON')
                    ->dateTime('F d, Y • h:i A')
                    ->sortable()
                    ->weight('semibold')
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
                ])

            ->filtersTriggerAction(fn (Action $action) => $action->button()->color('info'))


            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50, 'all'])
            ->searchable()

            ->emptyStateHeading('No Grievance Records Found')
            ->emptyStateDescription('There are currently no grievance submissions within the selected date range or applied filters.')
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateActions([
                Action::make('refresh')
                    ->label('Refresh')
                    ->button()
                    ->color('primary')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn () => $this->dispatch('$refresh')),
            ]);
    }
}
