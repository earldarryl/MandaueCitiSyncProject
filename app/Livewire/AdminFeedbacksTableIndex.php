<?php

namespace App\Livewire;

use App\Models\Feedback;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
class AdminFeedbacksTableIndex extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Feedback::with('user')->latest())
            ->columns([
                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('F j, Y')
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('region')
                    ->label('Region')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('service')
                    ->label('Service')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('suggestions')
                    ->label('Suggestions')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('answers')
                    ->label('Answers')
                    ->getStateUsing(fn ($record) => is_array($record->answers) ? implode(', ', $record->answers) : $record->answers)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ]),

                SelectFilter::make('region')
                    ->options([
                        'Region 1' => 'Region 1',
                        'Region 2' => 'Region 2',
                        'Region 3' => 'Region 3',
                    ]),

                SelectFilter::make('service')
                    ->options([
                        'Customer Service' => 'Customer Service',
                        'Technical Support' => 'Technical Support',
                        'HR Support' => 'HR Support',
                    ]),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
