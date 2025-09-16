<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn; // Import ImageColumn
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

    public function getProfilePicAttribute($value)
    {
        return $value ?: null;
    }


    protected function getTableColumns(): array
    {
        return [
            ImageColumn::
            make('profile_pic')
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
                    TextInput::make('name')
                        ->required(),

                    TextInput::make('email')
                        ->email()
                        ->required(),

                    TextInput::make('status')
                        ->label('Status')
                        ->default('offline'),

                    \Filament\Forms\Components\FileUpload::make('profile_pic')
                        ->disk('public')
                        ->directory('profile_pics')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1'])
                        ->panelLayout('integrated')
                        ->label('Profile Picture'),

                    \Filament\Forms\Components\Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->multiple(false)
                        ->required(),
                ])
                ->modalHeading('Edit User')
                ->modalButton('Save Changes')
                ->action(fn (User $record, array $data) => $record->update($data)),

            DeleteAction::make(),
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

    protected function getTablePaginationOptions(): ?int
    {
        return 10;
    }
}
