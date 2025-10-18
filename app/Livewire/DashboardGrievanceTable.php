<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DashboardGrievanceTable extends TableWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function table(Table $table): Table
    {
        $user = Auth::user();

        // Base query
        $query = Grievance::query()->with('user');

        // Role-based visibility
        if ($user->hasRole('hr_liaison')) {
            $query->whereHas('assignments', function (Builder $q) use ($user) {
                $q->where('hr_liaison_id', $user->id);
            });
        }

        // Global date filter
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('grievance_title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('grievance_status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('grievance_type')
                    ->label('Type')
                    ->sortable(),

                TextColumn::make('priority_level')
                    ->label('Priority')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn (Grievance $record) => route(
                        'hr-liaison.grievance.view',
                        ['id' => $record->grievance_id]
                    ))
                    ->icon('heroicon-o-eye'),
            ])
            ->searchable()
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50]);
    }
}
