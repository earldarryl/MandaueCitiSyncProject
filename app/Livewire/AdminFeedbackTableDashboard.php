<?php

namespace App\Livewire;

use App\Models\Feedback;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;
class AdminFeedbackTableDashboard extends TableWidget
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
                        <span class="font-semibold text-gray-800 dark:text-gray-300">region</span>, and
                        <span class="font-semibold text-gray-800 dark:text-gray-300">service category</span>.
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
                TextColumn::make('id')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">ID</span>'))
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('user_id')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">User ID</span>'))
                    ->default('Anonymous')
                    ->sortable(),

                TextColumn::make('service')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Service</span>'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('region')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Region</span>'))
                    ->sortable(),

                TextColumn::make('gender')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Gender</span>'))
                    ->badge()
                    ->colors([
                        'pink' => 'Female',
                        'blue' => 'Male',
                        'gray' => 'Other',
                    ])
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Date Submitted</span>'))
                    ->dateTime('F d, Y • h:i A')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('suggestions')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Suggestion / Comment</span>'))
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->suggestions ?? 'No comment')
                    ->wrap(),
            ])

            ->filters([
                SelectFilter::make('region')
                    ->label('Region')
                    ->options(
                        Feedback::select('region')
                            ->distinct()
                            ->pluck('region', 'region')
                            ->toArray()
                    ),

                SelectFilter::make('gender')
                    ->label('Gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ]),
                ])

            ->filtersTriggerAction(fn (Action $action) => $action->button()->color('info'))

            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50, 'all'])
            ->searchable()

            ->emptyStateHeading('No Feedback Records Found')
            ->emptyStateDescription('There are currently no feedback submissions within the selected date range or filters.')
            ->emptyStateIcon('heroicon-o-face-frown')
            ->emptyStateActions([
                Action::make('refresh')
                    ->label('Refresh')
                    ->button()
                    ->color('info')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn() => $this->dispatch('$refresh')),
            ]);
    }
}
