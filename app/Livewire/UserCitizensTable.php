<?php

namespace App\Livewire;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class UserCitizensTable extends TableWidget
{
    protected static ?string $heading = 'Citizens';
    protected static ?int $sort = 1;

    protected function getTableCheckboxColumn(): bool
    {
        return true;
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(fn (): Builder => User::role('citizen'))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('roles') // use the relationship name
                    ->label('Roles')
                    ->formatStateUsing(function ($record) {
                        // $record is the User model
                        $roles = $record->roles; // get all roles collection
                        if ($roles->isEmpty()) {
                            return '-';
                        }
                        return $roles->pluck('name')->implode(', ');
                    })
                    ->sortable(),
                TextColumn::make('created_at')->label('Created At')->dateTime('M d, Y'),
                ]);
    }
}
