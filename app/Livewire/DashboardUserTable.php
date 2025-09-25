<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Grievance;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardUserTable extends TableWidget
{
    public $tableType = 'users'; // Default table type

    // ----------------------
    // Main table builder
    // ----------------------
    public function table(Table $table): Table
    {
        TextColumn::configureUsing(fn (TextColumn $column) => $column->toggleable());

        return $table
            ->header(view('tables.header', array_merge(
                $this->getStats(),
                ['tableType' => $this->tableType]
            )))
            ->striped()
            ->query(fn (): Builder => $this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->actions($this->getTableActions())
            ->bulkActions($this->getTableBulkActions())
            ->searchable()
            ->reorderable('sort_order')
            ->recordTitle(fn ($record) => $record->name ?? $record->grievance_title)
            ->defaultSort('created_at', 'desc')
            ->deferLoading()
            ->paginated([5, 10, 25, 50, 100, 'all'])
            ->poll(10);
    }

    // ----------------------
    // Dynamic header stats
    // ----------------------
    protected function getStats(): array
    {
        $user = auth()->user();

        $totalUsers = $user->hasRole('admin')
            ? User::count()
            : User::whereHas('roles', fn($q) => $q->where('name','citizen'))->count();

        $activeUsers = $user->hasRole('admin')
            ? User::where('last_seen_at', '>=', now()->subMinutes(5))->count()
            : User::whereHas('roles', fn($q) => $q->where('name','citizen'))
                  ->where('last_seen_at', '>=', now()->subMinutes(5))
                  ->count();

        $todayUsers = $user->hasRole('admin')
            ? User::whereDate('created_at', today())->count()
            : User::whereHas('roles', fn($q) => $q->where('name','citizen'))
                  ->whereDate('created_at', today())
                  ->count();

        $totalGrievances = $user->hasRole('admin')
            ? Grievance::count()
            : Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))->count();

        $pendingGrievances = $user->hasRole('admin')
            ? Grievance::where('grievance_status','pending')->count()
            : Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
                  ->where('grievance_status','pending')
                  ->count();

        $resolvedGrievances = $user->hasRole('admin')
            ? Grievance::where('grievance_status','resolved')->count()
            : Grievance::whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
                  ->where('grievance_status','resolved')
                  ->count();

        return [
            'heading' => $this->tableType === 'users' ? 'Users' : 'Grievances',
            'totalUsers' => $this->tableType === 'users' ? $totalUsers : $totalGrievances,
            'activeUsers' => $this->tableType === 'users' ? $activeUsers : $pendingGrievances,
            'todayUsers' => $this->tableType === 'users' ? $todayUsers : $resolvedGrievances,
        ];
    }

    // ----------------------
    // Query per role
    // ----------------------
    protected function getTableQuery(): Builder|\Illuminate\Database\Eloquent\Relations\Relation|null
    {
        $user = auth()->user();

        if ($this->tableType === 'users') {
            if ($user->hasRole('admin')) {
                return User::query()->with('roles');
            }

            if ($user->hasRole('hr_liaison')) {
                return User::query()
                    ->whereHas('roles', fn ($q) => $q->where('name', 'citizen'))
                    ->with('roles');
            }

            return User::query()->with('roles');
        }

        if ($this->tableType === 'grievances') {
            if ($user->hasRole('admin')) {
                return Grievance::query()->with('user');
            }

            if ($user->hasRole('hr_liaison')) {
                return Grievance::query()
                    ->whereHas('assignments', fn($q) => $q->where('hr_liaison_id', $user->id))
                    ->with('user');
            }

            return Grievance::query()->with('user');
        }

        return User::query()->with('roles');
    }

    // ----------------------
    // Columns
    // ----------------------
    protected function getTableColumns(): array
    {
        if ($this->tableType === 'users') {
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

        return [
            TextColumn::make('grievance_title')->label('Title')->sortable()->searchable()->reorderable(),
            TextColumn::make('category')->label('Category')->sortable()->reorderable(),
            TextColumn::make('grievance_status')->label('Status')->badge()->sortable()->reorderable(),
            TextColumn::make('grievance_type')->label('Type')->sortable()->reorderable(),
            TextColumn::make('user.name')->label('Submitted By')->sortable()->reorderable(),
            TextColumn::make('created_at')->label('Created')->dateTime('M d, Y')->sortable()->reorderable(),
        ];
    }

    // ----------------------
    // Filters
    // ----------------------
    protected function getTableFilters(): array
    {
        $user = Auth::user();

        if ($this->tableType === 'users') {
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

        if ($this->tableType === 'grievances') {
            return [
                SelectFilter::make('grievance_status')->options([
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'resolved' => 'Resolved',
                ])->label('Status')->native(false),

                Filter::make('created_today')->label('Created Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today())),
            ];
        }

        return [];
    }

    // ----------------------
    // Bulk actions
    // ----------------------
    protected function getTableBulkActions(): array
    {
        $user = Auth::user();

        if ($this->tableType === 'users' && $user->hasRole('admin')) {
            return [
                BulkActionGroup::make([
                    BulkAction::make('delete')->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete())
                        ->successNotificationTitle('Deleted users'),
                ])
            ];
        }

        return [];
    }
}
