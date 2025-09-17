<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;

class DashboardTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Users Registered';
    public function getTable(): Table
    {
        return Table::make($this)
            ->query(User::query()->latest('created_at'))
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->actions($this->getTableActions())
            ->bulkActions($this->getTableBulkActions())
            ->paginated([10]);
    }

    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('profile_pic')
                ->label('Profile')
                ->circular()
                ->size(40)
                ->getStateUsing(fn ($record) =>
                    $record->profile_pic
                        ? asset('storage/' . $record->profile_pic)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
                ),
            TextColumn::make('name')
                ->label('Name')
                ->sortable()
                ->searchable()
                ->weight('medium'),
            TextColumn::make('email')
                ->label('Email')
                ->sortable()
                ->searchable(),
            TextColumn::make('roles.name')
                ->label('Role')
                ->badge()
                ->sortable()
                ->formatStateUsing(fn ($state) => $state
                    ? ($state === 'hr_liaison' ? 'HR Liaison' : Str::headline($state))
                    : null
                )
                ->color(fn (User $record) => match ($record->roles->first()?->name ?? '') {
                    'admin' => 'danger',
                    'hr_liaison' => 'info',
                    'citizen' => 'success',
                    default => 'secondary',
                }),
            IconColumn::make('status')
                ->label('Online')
                ->boolean()
                ->trueIcon('heroicon-o-bolt')
                ->falseIcon('heroicon-o-x-circle')
                ->color(fn (User $record) => match ($record->status) {
                    'online' => 'success',
                    'away' => 'warning',
                    'offline' => 'secondary',
                    default => 'secondary',
                })
                ->tooltip(fn (User $record) => ucfirst($record->status)),
            TextColumn::make('created_at')
                ->label('Joined')
                ->dateTime('M d, Y')
                ->sortable()
                ->color('gray'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('roles')
                ->label('Role')
                ->relationship('roles', 'name')
                ->column('roles.id')
                ->indicator('Role')
                ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                    'admin' => 'Admin',
                    'hr_liaison' => 'HR Liaison',
                    'citizen' => 'Citizen',
                    default => Str::headline(str_replace('_', ' ', $record->name)),
                })
                ->indicateUsing(function ($state) {
                    if (! $state) return null;

                    $names = is_array($state)
                        ? \DB::table('roles')->whereIn('id', $state)->pluck('name')->toArray()
                        : [\DB::table('roles')->where('id', $state)->value('name') ?? $state];

                    return collect($names)->map(fn ($name) => match ($name) {
                        'admin' => 'Admin',
                        'hr_liaison' => 'HR Liaison',
                        'citizen' => 'Citizen',
                        default => Str::headline(str_replace('_', ' ', $name)),
                    })->implode(', ');
                }),
            Tables\Filters\SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'online' => 'Online',
                    'away' => 'Away',
                    'offline' => 'Offline',
                ])
                ->indicator('Status'),
            Tables\Filters\Filter::make('created_at')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('From'),
                    Forms\Components\DatePicker::make('until')->label('Until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                }),
            Tables\Filters\Filter::make('updated_at')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('From'),
                    Forms\Components\DatePicker::make('until')->label('Until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('updated_at', '>=', $date))
                        ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('updated_at', '<=', $date));
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil-square')
                ->button()
                ->color('info')
                ->form([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->email()->required(),
                    TextInput::make('status')->label('Status')->default('offline'),
                    Forms\Components\FileUpload::make('profile_pic')
                        ->disk('public')
                        ->directory('profile_pics')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1'])
                        ->panelLayout('integrated')
                        ->label('Profile Picture'),
                    Forms\Components\Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->multiple(false)
                        ->required(),
                ])
                ->modalHeading('Edit User')
                ->modalButton('Save Changes')
                ->action(fn (User $record, array $data) => $record->update($data)),
            DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->button()
                ->color('danger'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ];
    }
}
