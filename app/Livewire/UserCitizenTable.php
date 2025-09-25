<?php

namespace App\Livewire;

use App\Models\User;
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

class UserCitizenTable extends TableWidget
{
    public function table(Table $table): Table
    {
        TextColumn::configureUsing(function (TextColumn $column): void {
            $column->toggleable();
        });

        return $table
            ->striped()
            ->poll('10s')
            ->header(view('tables.header', [
                'heading'     => 'Users',
                'totalUsers'  => User::role('citizen')->count(),
                'activeUsers' => User::role('citizen')
                    ->whereNotNull('email_verified_at')
                    ->count(),
                'todayUsers'  => User::role('citizen')
                    ->whereDate('created_at', today())
                    ->count(),
            ]))
            ->description('Manage your users here.')
            ->query(fn (): Builder => User::whereHas('roles', fn ($q) => $q->where('name', 'citizen')))
            ->reorderable('sort_order')
            ->reorderableColumns()
            ->columns([
                ImageColumn::make('profile_pic')
                    ->label('Profile')
                    ->circular()
                    ->size(40)
                    ->alignCenter(true)
                    ->getStateUsing(fn ($record) =>
                        $record->profile_pic
                            ? asset('storage/' . $record->profile_pic)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
                    ),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->weight('medium')
                    ->alignCenter(true),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(true),
                IconColumn::make('status')
                    ->label('Online')
                    ->boolean()
                    ->alignCenter(true)
                    ->trueIcon('heroicon-o-bolt')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn ($record) => match ($record->status) {
                        'online' => 'success',
                        'away' => 'warning',
                        'offline' => 'secondary',
                        default => 'secondary',
                    })
                    ->tooltip(fn ($record) => ucfirst($record->status)),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter(true)
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'online' => 'Online',
                        'away' => 'Away',
                        'offline' => 'Offline',
                    ])
                    ->label('Online Status')
                    ->native(false),
                Filter::make('joined')
                    ->label('Joined Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', now()->today())),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->infolist([
                            Section::make('User Details')
                                ->schema([
                                    Grid::make(1)
                                        ->schema([
                                            ImageEntry::make('profile_pic')
                                                ->label('Profile Picture')
                                                ->circular()
                                                ->hiddenLabel()
                                                ->getStateUsing(fn ($record) =>
                                                    $record->profile_pic
                                                        ? asset('storage/' . $record->profile_pic)
                                                        : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
                                                )
                                                ->alignCenter(),
                                        ]),
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label('Full Name')
                                                ->icon('heroicon-o-user')
                                                ->weight('bold'),
                                            TextEntry::make('email')
                                                ->label('Email Address')
                                                ->icon('heroicon-o-envelope'),
                                            TextEntry::make('status')
                                                ->label('Status')
                                                ->badge()
                                                ->color(fn ($state) => match ($state) {
                                                    'online' => 'success',
                                                    'away' => 'warning',
                                                    'offline' => 'secondary',
                                                    default => 'gray',
                                                })
                                                ->formatStateUsing(fn ($state) => ucfirst($state)),
                                            TextEntry::make('created_at')
                                                ->label('Joined On')
                                                ->icon('heroicon-o-calendar')
                                                ->dateTime('M d, Y'),
                                        ]),
                                ])->columns(2),
                        ]),
                    EditAction::make()
                        ->form([
                            FileUpload::make('profile_pic')
                                ->label('Profile Picture')
                                ->directory('profiles')
                                ->image()
                                ->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth(300)
                                ->imageResizeTargetHeight(300),

                            TextInput::make('name')
                                ->label('Name')
                                ->required(),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required(),

                            // ✅ Removed Role selector
                            Select::make('status')
                                ->options([
                                    'online' => 'Online',
                                    'away' => 'Away',
                                    'offline' => 'Offline',
                                ])
                                ->label('Status'),
                        ]),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete())
                        ->successNotificationTitle('Deleted users')
                        ->failureNotificationTitle(function (int $successCount, int $totalCount): string {
                            if ($successCount) {
                                return "{$successCount} of {$totalCount} users deleted";
                            }
                            return 'Failed to delete any users';
                        }),
                ]),
            ])
            ->deferLoading()
            ->searchable()
            ->recordTitle('name')
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25, 50, 100, 'all']);
    }
}
