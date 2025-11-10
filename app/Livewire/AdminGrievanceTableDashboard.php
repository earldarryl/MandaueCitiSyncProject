<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
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
                    ->color('info')
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_title')
                    ->label('TITLE')
                    ->sortable()
                    ->searchable()
                    ->weight('semibold')
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                 TextColumn::make('grievance_type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'danger' => 'Complaint',
                        'info' => 'Request',
                        'success' => 'Inquiry',
                    ])
                    ->weight('bold')
                    ->sortable()
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('grievance_status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->extraAttributes(fn (string $state): array => [
                        'class' => 'inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full border shadow-sm ' . match ($state) {
                            'pending' => 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600',
                            'acknowledged' => 'bg-indigo-100 text-indigo-800 border-indigo-400 dark:bg-indigo-900/40 dark:text-indigo-300 dark:border-indigo-500',
                            'in_progress' => 'bg-blue-100 text-blue-800 border-blue-400 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-500',
                            'escalated' => 'bg-amber-100 text-amber-800 border-amber-400 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-500',
                            'resolved' => 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500',
                            'unresolved' => 'bg-red-100 text-red-800 border-red-400 dark:bg-red-900/40 dark:text-red-300 dark:border-red-500',
                            'closed' => 'bg-purple-100 text-purple-800 border-purple-400 dark:bg-purple-900/40 dark:text-purple-300 dark:border-purple-500',
                            default => 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600',
                        },
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ])
                    ->sortable(),

                BadgeColumn::make('grievance_status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'acknowledged' => 'info',
                        'in_progress' => 'warning',
                        'escalated' => 'danger',
                        'resolved' => 'success',
                        'unresolved' => 'danger',
                        'closed' => 'secondary',
                        default => 'gray',
                    })
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ])
                    ->sortable(),


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
                    ->colors(['info'])
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->weight('semibold')
                    ->searchable()
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('user.name')
                    ->label('SUBMITTED BY')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->is_anonymous
                            ? 'Anonymous'
                            : ($record->user?->info
                                ? trim(
                                    $record->user->info->first_name
                                    . ' '
                                    . ($record->user->info->middle_name ? $record->user->info->middle_name[0] . '. ' : '')
                                    . $record->user->info->last_name
                                    . ($record->user->info->suffix ? ' ' . $record->user->info->suffix : '')
                                )
                                : $record->user?->name
                            )
                    )
                    ->tooltip(fn ($record) =>
                        $record->is_anonymous
                            ? 'Anonymous Grievance'
                            : ($record->user?->info
                                ? trim(
                                    $record->user->info->first_name
                                    . ' '
                                    . ($record->user->info->middle_name ? $record->user->info->middle_name[0] . '. ' : '')
                                    . $record->user->info->last_name
                                    . ($record->user->info->suffix ? ' ' . $record->user->info->suffix : '')
                                )
                                : $record->user?->name
                            )
                    )
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold',
                    ])
                    ->extraHeaderAttributes([
                        'class' => 'uppercase text-gray-600 dark:text-gray-300 tracking-wide text-[12px] font-bold',
                    ]),

                TextColumn::make('created_at')
                    ->label('CREATED ON')
                    ->dateTime('F d, Y • h:i A')
                    ->sortable()
                    ->extraAttributes([
                        'class' => 'text-[12px] font-bold',
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
            ->emptyStateIcon('heroicon-o-folder-open');
    }
}
