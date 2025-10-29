<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Actions\Action;
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

    protected function getSummaryData(): array
    {
        $query = $this->baseQuery();

        return [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->where('grievance_status', 'Open')->count(),
            'resolved' => (clone $query)->where('grievance_status', 'Resolved')->count(),
            'highPriority' => (clone $query)->where('priority_level', 'High')->count(),
        ];
    }

    public function getHeading(): string | Htmlable | null
    {
        return new HtmlString(<<<'HTML'
            <div class="flex flex-col gap-2 w-full p-4 bg-blue-50/60 dark:bg-blue-950/30
                        border border-blue-200 dark:border-blue-800 rounded-xl shadow-sm">
                <!-- Header -->
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-blue-700 dark:text-blue-300">
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

                <!-- Accordion -->
                <div x-data="{ open: false }" class="mt-1">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-sm font-medium text-blue-700 dark:text-blue-300
                            hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <span>About this table</span>
                        <svg xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-180': open }"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse
                        class="mt-2 text-sm text-blue-800 dark:text-blue-200 bg-blue-100/50 dark:bg-blue-900/40
                            rounded-lg p-3 border border-blue-200 dark:border-blue-800">
                        This table lists all <span class="font-semibold text-blue-900 dark:text-blue-100">grievances</span>
                        assigned to HR liaisons, including both
                        <span class="font-semibold text-blue-900 dark:text-blue-100">identified</span> and
                        <span class="font-semibold text-blue-900 dark:text-blue-100">anonymous</span> submissions.
                        <br><br>
                        <span class="text-blue-900 dark:text-blue-100 font-medium">Purpose:</span>
                        To provide an overview of grievance records, their types, priorities, and identities —
                        enabling HR to track progress and analyze communication transparency.
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
            // Pass the HTML directly so the accordion heading shows up
            ->header($this->getHeading())
            ->columns([
                TextColumn::make('grievance_title')
                    ->label('Title')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('grievance_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match (strtolower($state)) {
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'rejected' => 'Rejected',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'resolved',
                        'danger' => 'rejected',
                    ])
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('grievance_type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'danger' => 'Complaint',
                        'info' => 'Request',
                        'success' => 'Inquiry',
                    ])
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('priority_level')
                    ->label('Priority')
                    ->badge()
                    ->colors([
                        'success' => 'Low',
                        'info' => 'Normal',
                        'danger' => 'High',
                    ])
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('is_anonymous')
                    ->label('Identity')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Anonymous' : 'Identified')
                    ->colors(fn ($record) => $record->is_anonymous ? ['gray'] : ['success'])
                    ->tooltip(fn ($record) => $record->is_anonymous
                        ? 'Submitted anonymously'
                        : $record->user?->email)
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? '—' : $state)
                    ->tooltip(fn ($record) => $record->is_anonymous
                        ? 'Anonymous grievance'
                        : $record->user?->email)
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y • h:i A')
                    ->weight('bold')
                    ->sortable(),
            ])
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
                    ->tooltip('View grievance details')
                    ->button(),
            ])
            ->searchable()
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50, 'all']);
    }
}
