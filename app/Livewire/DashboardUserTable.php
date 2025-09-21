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
            ->striped()
            ->query(fn (): Builder => $this->tableType === 'users'
                ? User::query()->with('roles')
                : Grievance::query()->with('user'))
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->actions($this->getTableActions())
            ->bulkActions($this->getTableBulkActions())
            ->searchable()
            ->reorderable('sort_order')
            ->recordTitle(fn ($record) => $record->name ?? $record->grievance_title)
            ->defaultSort('created_at', 'desc')
            ->deferLoading() // deferred to improve heavy datasets
            ->paginated([5, 10, 25, 50, 100, 'all']);
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
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ];
        }

        // Grievances table
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
        if ($this->tableType === 'users') {
            return [
                SelectFilter::make('roles')->relationship('roles', 'name')->multiple()->preload()->label('Role'),
                SelectFilter::make('status')->options([
                    'online' => 'Online',
                    'away' => 'Away',
                    'offline' => 'Offline',
                ])->label('Online Status')->native(false),
                Filter::make('joined')->label('Joined Today')->query(fn (Builder $query) => $query->whereDate('created_at', today())),
            ];
        }

        return [
            SelectFilter::make('grievance_status')->options([
                'pending' => 'Pending',
                'processing' => 'Processing',
                'resolved' => 'Resolved',
            ])->label('Status')->native(false),
            Filter::make('created_today')->label('Created Today')->query(fn (Builder $query) => $query->whereDate('created_at', today())),
        ];
    }

    // ----------------------
    // Actions
    // ----------------------
    protected function getTableActions(): array
    {
        if ($this->tableType === 'users') {
            return [
                ActionGroup::make([
                    ViewAction::make()->infolist([
                        Section::make('User Details')->schema([
                            Grid::make(1)->schema([
                                ImageEntry::make('profile_pic')->label('Profile Picture')->circular()->alignCenter()
                                    ->getStateUsing(fn ($record) => $record->profile_pic
                                        ? asset('storage/' . $record->profile_pic)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                            ]),
                            Grid::make(2)->schema([
                                TextEntry::make('name')->label('Full Name'),
                                TextEntry::make('email')->label('Email'),
                                TextEntry::make('roles')->label('Role')
                                    ->formatStateUsing(fn ($state, $record) => $record->roles->map(fn ($role) => Str::headline($role->name))->implode(', ')),
                                TextEntry::make('status')->label('Status'),
                                TextEntry::make('created_at')->label('Joined On'),
                            ]),
                        ])
                    ]),
                    EditAction::make()->form([
                        FileUpload::make('profile_pic')->directory('profiles')->image()->imageCropAspectRatio('1:1'),
                        TextInput::make('name')->label('Name')->required(),
                        TextInput::make('email')->label('Email')->email()->required(),
                        Select::make('roles')->relationship('roles', 'name')->multiple()->preload()->label('Role'),
                        Select::make('status')->options(['online'=>'Online','away'=>'Away','offline'=>'Offline'])->label('Status'),
                    ]),
                    DeleteAction::make(),
                ])
            ];
        }

        return [
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
        ];
    }

    // ----------------------
    // Bulk actions
    // ----------------------
    protected function getTableBulkActions(): array
    {
        if ($this->tableType === 'users') {
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
