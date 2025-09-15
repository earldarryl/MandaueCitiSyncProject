<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class DashboardTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return User::query()->latest('created_at');
    }

    protected function getTableColumns(): array
    {
        return [
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
            Tables\Filters\SelectFilter::make('role')
                ->label('Role')
                ->options([
                    'admin' => 'Admin',
                    'hr_liaison' => 'HR Liaison',
                    'citizen' => 'Citizen',
                ])
                ->indicator('Role'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make('edit')
                ->label('Edit User')
                ->form([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->email()->required(),
                ])
                ->modalHeading('Edit User')
                ->modalButton('Save Changes')
                ->action(fn (User $record, array $data) => $record->update($data)),

            DeleteAction::make(), // ⬅️ Corrected: Use a single DeleteAction for row-level actions.
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(), // ⬅️ Corrected: Use DeleteBulkAction for bulk actions.
            ]),
        ];
    }

    protected function getTablePaginationOptions(): ?int
    {
        return 10; // records per page
    }
}
