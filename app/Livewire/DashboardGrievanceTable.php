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

    protected function baseQuery(): Builder
    {
        $user = Auth::user();

        $query = Grievance::query()->with('user');

        if ($user->hasRole('hr_liaison')) {
            $query->whereHas('assignments', function (Builder $q) use ($user) {
                $q->where('hr_liaison_id', $user->id);
            });
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
        }

        return $query;
    }

    protected function getSummaryData(): array
    {
        $query = $this->baseQuery();

        return [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->where('grievance_status', 'Open')->count(),
            'resolved' => (clone $query)->where('grievance_status', 'Resolved')->count(),
            'highPriority' => (clone $query)->where('priority_level', 'High')->count(),
        ];
    }

    public function table(Table $table): Table
    {
        $query = $this->baseQuery();

        return $table
            ->query($query)
            ->header(function () {
                $summary = $this->getSummaryData();

                return view('widget-headers.grievance-table-header-summary', [
                    'summary' => $summary,
                ]);
            })
            ->columns([
                TextColumn::make('grievance_title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('grievance_status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'rejected' => 'Rejected',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'resolved',
                        'danger' => 'rejected',
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grievance_type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'danger' => 'Complaint',
                        'info' => 'Request',
                        'success' => 'Inquiry',
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('priority_level')
                    ->label('Priority')
                    ->badge()
                    ->colors([
                        'success' => 'Low',
                        'info' => 'Normal',
                        'danger' => 'High',
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Anonymous User' : $state)
                    ->tooltip(fn ($record) => $record->is_anonymous ? 'Anonymous grievance' : $record->user?->email),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn (Grievance $record) => route('hr-liaison.grievance.view', ['id' => $record->grievance_id]))
                    ->icon('heroicon-o-eye'),
            ])
            ->searchable()
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50, 'all']);
    }
}
