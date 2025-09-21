<?php

namespace App\Livewire;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use App\Models\Grievance;
use Filament\Tables\Columns\TextColumn;
class DashboardGrievanceTable extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Grievance::with('user'))
            ->columns([
                TextColumn::make('grievance_title')->label('Title')->sortable(),
                TextColumn::make('category')->label('Category')->sortable(),
                TextColumn::make('grievance_status')->label('Status')->badge()->sortable(),
                TextColumn::make('grievance_type')->label('Type')->sortable(),
                TextColumn::make('user.name')->label('Submitted By')->sortable(),
                TextColumn::make('created_at')->label('Created')->dateTime('M d, Y')->sortable(),
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
                    ])
                ])
            ])
            ->searchable()
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50]);
    }
}
