<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AdminStakeholdersHrLiaisonsListView extends TableWidget
{
    // Make the property nullable, so PHP won't complain if it's not set yet
    public ?int $department = null;

    public function getHeading(): string | Htmlable | null
    {
        $deptText = $this->department ? "Department #{$this->department}" : "All Departments";

        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full p-3">
                <div class="flex items-center justify-between w-full">
                    <h2 class="text-lg font-bold text-gray-700 dark:text-gray-100">
                        Registered HR Liaisons of {$deptText}
                    </h2>
                </div>
            </div>
        HTML);
    }

    public function table(Table $table): Table
    {
        TextColumn::configureUsing(fn (TextColumn $column) => $column->toggleable());

        return $table
            ->header($this->getHeading())
            ->poll('10s')
            ->striped()
            ->deferLoading()
            ->description('Manage all registered HR Liaisons.')
            ->query(fn (): Builder => $this->getQuery())
            ->columns([
                ImageColumn::make('profile_pic')
                    ->label('Profile')
                    ->circular()
                    ->size(42)
                    ->alignCenter()
                    ->getStateUsing(fn ($record) =>
                        $record->profile_pic
                            ? asset('storage/' . $record->profile_pic)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
                    ),

                TextColumn::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable()
                    ->weight('semibold')
                    ->alignCenter(),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter()
                    ->trueIcon('heroicon-o-bolt')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn ($record) => match ($record->status) {
                        'online' => 'success',
                        'away' => 'warning',
                        'offline' => 'secondary',
                        default => 'secondary',
                    })
                    ->tooltip(fn ($record) => ucfirst($record->status)),

                TextColumn::make('created_at')
                    ->label('Date Joined')
                    ->dateTime('F d, Y • h:i A')
                    ->alignCenter()
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'online' => 'Online',
                        'away' => 'Away',
                        'offline' => 'Offline',
                    ])
                    ->label('Online Status')
                    ->native(false),

                Filter::make('joined_today')
                    ->label('Joined Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', now()->today())),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('View Details')->button(),
                    EditAction::make()->label('Edit User')->button(),
                    DeleteAction::make()->label('Delete User')->button(),
                ])
                ->label('Actions')
                ->button()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('Delete Selected')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete()),
                ])
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->paginated([5, 10, 25, 50, 100, 'all']);
    }

    protected function getQuery(): Builder
{
    $query = User::query()
        ->whereHas('roles', fn ($q) => $q->where('name', 'hr_liaison'));

    if ($this->department) {
        $query->whereHas('departments', fn ($q) =>
            $q->where('hr_liaison_departments.department_id', $this->department)
        );
    }

    return $query;
}

}
