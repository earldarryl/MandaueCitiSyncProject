<?php

namespace App\Livewire;

use App\Models\Grievance;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
class AdminGrievancesTableIndex extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Grievance::with('user')->latest())
            ->columns([
                TextColumn::make('grievance_ticket_id')
                    ->label('Ticket ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('grievance_title')
                    ->label('Title')
                    ->sortable()
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                BadgeColumn::make('priority_level')
                    ->label('Priority')
                    ->colors([
                        'primary' => Grievance::PRIORITY_LOW,
                        'warning' => Grievance::PRIORITY_NORMAL,
                        'danger' => Grievance::PRIORITY_HIGH,
                    ])
                    ->sortable(),

                BadgeColumn::make('grievance_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Resolved',
                        'warning' => 'In Progress',
                        'danger' => 'Pending',
                    ])
                    ->sortable(),

                TextColumn::make('grievance_category')
                    ->label('Category')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime('F j, Y - g:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('priority_level')
                    ->options([
                        Grievance::PRIORITY_LOW => 'Low',
                        Grievance::PRIORITY_NORMAL => 'Normal',
                        Grievance::PRIORITY_HIGH => 'High',
                    ]),

                SelectFilter::make('grievance_status')
                    ->options([
                        'Pending' => 'Pending',
                        'In Progress' => 'In Progress',
                        'Resolved' => 'Resolved',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Submitted By')
                    ->relationship('user', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
