<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
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
                $this->endDate . ' 23:59:59'
            ]);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('grievance_title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('grievance_status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('grievance_type')
                    ->label('Type')
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
                ViewAction::make()->infolist([
                    Section::make('Grievance Details')->schema([
                        TextEntry::make('grievance_title')->label('Title'),
                        TextEntry::make('category')->label('Category'),
                        TextEntry::make('grievance_type')->label('Type'),
                        TextEntry::make('grievance_status')->label('Status'),
                        TextEntry::make('grievance_details')->label('Details'),
                        TextEntry::make('user.name')->label('Submitted By'),
                    ]),
                ]),
            ])
            ->searchable()
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50]);
    }
}
