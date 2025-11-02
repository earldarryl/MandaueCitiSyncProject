<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardUserTable extends TableWidget
{
    protected static bool $shouldPersistTableFiltersInSession = true;
    protected static bool $shouldPersistTableSortInSession = true;
    protected static bool $shouldPersistTablePaginationInSession = true;

    public function table(Table $table): Table
    {
        TextColumn::configureUsing(fn (TextColumn $column) => $column->toggleable());

        return $table
            ->header(view('tables.header', $this->getStats()))
            ->striped()
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->searchable()
            ->recordTitle(fn ($record) => $record->name)
            ->paginated([5, 10, 25, 50, 100, 'all']);
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $totalUsers = User::count();
            $activeUsers = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
            $todayUsers = User::whereDate('created_at', today())->count();
        } else {
            $totalUsers = User::where('last_seen_at', '>=', now()->subDays(7))->count();
            $activeUsers = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
            $todayUsers = User::whereDate('created_at', today())->count();
        }

        return [
            'heading' => 'Users',
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'todayUsers' => $todayUsers,
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return User::query()->with('roles');
        }

        return User::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->with('roles');
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
            TextColumn::make('name')->label('Name')->sortable()->searchable(),
            TextColumn::make('email')->label('Email')->sortable()->searchable(),
            TextColumn::make('roles.name')
                ->label('Role')
                ->badge()
                ->sortable()
                ->formatStateUsing(fn ($state, $record) =>
                    $record->roles->map(fn ($role) => Str::headline($role->name))->implode(', ')
                ),
            IconColumn::make('status')
                ->label('Online')
                ->boolean()
                ->trueIcon('heroicon-o-bolt')
                ->falseIcon('heroicon-o-x-circle')
                ->color(fn ($record) => match ($record->status) {
                    'online' => 'success',
                    'away' => 'warning',
                    'offline' => 'secondary',
                    default => 'secondary'
                })
                ->tooltip(fn ($record) => ucfirst($record->status)),
            TextColumn::make('created_at')->label('Joined')->dateTime('M d, Y')->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('roles')->relationship('roles', 'name')->multiple()->preload()->label('Role'),
            SelectFilter::make('status')
                ->label('Online Status')
                ->options([
                    'online' => 'Online',
                    'away' => 'Away',
                    'offline' => 'Offline',
                ])
                ->query(function (Builder $query, array $data) {
                    if (!isset($data['value'])) return;

                    if ($data['value'] === 'online') {
                        $query->where('last_seen_at', '>=', now()->subMinutes(5));
                    } elseif ($data['value'] === 'away') {
                        $query->where('last_seen_at', '<', now()->subMinutes(5))
                              ->whereNotNull('last_seen_at');
                    } elseif ($data['value'] === 'offline') {
                        $query->whereNull('last_seen_at');
                    }
                }),
            Filter::make('joined')->label('Joined Today')
                ->query(fn (Builder $query) => $query->whereDate('created_at', today())),
        ];
    }
}
