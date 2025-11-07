<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
class AdminStakeholdersDepartmentsAndHrLiaisonsTable extends TableWidget
{
    protected static ?string $pollingInterval = null;

    public function getHeading(): string|Htmlable|null
    {
        return new HtmlString(<<<HTML
            <div class="flex flex-col w-full p-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-700 dark:text-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 text-blue-600 dark:text-blue-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v9a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 16.5v-9Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9 6 9-6" />
                        </svg>
                        <span>Departments & HR Liaisons</span>
                    </h2>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    A detailed view of all departments and their assigned HR liaisons.
                </p>
            </div>
        HTML);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Department::query()->with('hrLiaisons'))
            ->columns([
                ImageColumn::make('department_profile_url')
                    ->label('Profile')
                    ->circular()
                    ->size(40),

                TextColumn::make('department_name')
                    ->label('Department Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('department_code')
                    ->label('Code')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'secondary')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),

                TextColumn::make('hr_liaisons_count')
                    ->label('HR Liaisons')
                    ->counts('hrLiaisons')
                    ->sortable(),
            ])
          ->actions([

    // View Liaisons
    Action::make('viewLiaisons')
        ->label('View Liaisons')
        ->url(fn ($record) => route(
            'admin.stakeholders.departments-and-hr-liaisons.hr-liaisons-list-view',
            ['department' => $record->department_id]
        ))
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
        ->icon('heroicon-o-eye')
        ->color('primary')
        ->tooltip('View the list of HR Liaisons for this department')
        ->button(),

    Action::make('addLiaison')
        ->label('Add HR Liaisons')
        ->icon('heroicon-o-user-plus')
        ->color('success')
        ->button()
        ->outlined()
        ->tooltip('Add HR Liaisons to this department')
        ->extraAttributes([
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
        ->closeModalByClickingAway(false)
        ->form([
            Forms\Components\Select::make('hr_liaison')
                ->label('Select HR Liaisons')
                ->multiple()
                ->options(fn ($get, $record) =>
                    User::role('hr_liaison')
                        ->whereDoesntHave('departments', function ($query) use ($record) {
                            $query->where('departments.department_id', $record->department_id);
                        })
                        ->pluck('name', 'id')
                )
                ->searchable(),
        ])
        ->action(function (array $data, Department $record) {
            $record->hrLiaisons()->attach($data['hr_liaison']);
        })
        ->after(function () {
            $this->resetTable();
            $this->dispatch('refresh');
        }),

    Action::make('editLiaison')
        ->label('Edit HR Liaisons')
        ->icon('heroicon-o-pencil-square')
        ->color('warning')
        ->button()
        ->outlined()
        ->tooltip('Edit assigned HR Liaisons')
        ->extraAttributes([
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
        ->closeModalByClickingAway(false)
        ->form(fn ($record) => [
            Forms\Components\Select::make('hr_liaison')
                ->label('Update HR Liaisons')
                ->multiple()
                ->options(fn ($get, $record) =>
                    User::role('hr_liaison')
                        ->where(function ($query) use ($record) {
                            $query->whereDoesntHave('departments', function ($q) use ($record) {
                                $q->where('hr_liaison_departments.department_id', $record->department_id);
                            })
                            ->orWhereHas('departments', function ($q) use ($record) {
                                $q->where('hr_liaison_departments.department_id', $record->department_id);
                            });
                        })
                        ->pluck('name', 'id')
                )
                ->default(fn ($record) => $record->hrLiaisons->pluck('id')->toArray())
                ->searchable(),
        ])
        ->action(function (array $data, Department $record) {
            $record->hrLiaisons()->sync($data['hr_liaison']);
        })
        ->after(function () {
            $this->resetTable();
            $this->dispatch('refresh');
        }),
])


            ->paginated([5, 10, 25, 50, 'all'])
            ->defaultSort('department_name', 'asc')
            ->poll('15s')
            ->striped()
            ->searchable()
            ->emptyStateHeading('No Departments Found')
            ->emptyStateDescription('There are currently no registered departments in the system.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}
