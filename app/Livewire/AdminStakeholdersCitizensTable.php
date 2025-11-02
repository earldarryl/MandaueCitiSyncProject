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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AdminStakeholdersCitizensTable extends TableWidget
{
    public function getHeading(): string | Htmlable | null
    {
        return new HtmlString(<<<HTML
            <div class="flex flex-col gap-2 w-full p-3">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-gray-700 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="size-6 text-blue-600 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 1 1-8 0m8 0a4 4 0 1 1-8 0m8 0v10a4 4 0 0 1-8 0V7" />
                            </svg>
                            <span>Registered Citizen Users</span>
                        </h2>
                    </div>
                </div>

                <div x-data="{ open: false }" class="mt-1">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full text-sm font-medium text-gray-700 dark:text-gray-200
                            hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <span>About this table</span>
                        <svg xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-180': open }"
                            class="w-4 h-4 transition-transform" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse
                        class="mt-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-zinc-800
                            rounded-lg p-3 border border-gray-200 dark:border-zinc-700">
                        This table lists all <span class="font-semibold text-gray-800 dark:text-gray-300">citizen users</span>
                        registered within the system, showing their <span class="font-semibold text-gray-800 dark:text-gray-300">profile</span>,
                        <span class="font-semibold text-gray-800 dark:text-gray-300">email</span>, and current
                        <span class="font-semibold text-gray-800 dark:text-gray-300">online status</span>.
                        <br><br>
                        <span class="text-gray-800 dark:text-gray-300 font-medium">Purpose:</span>
                        To allow administrators to monitor, edit, and manage registered users efficiently.
                    </div>
                </div>
            </div>
        HTML);
    }

    public function table(Table $table): Table
    {
        TextColumn::configureUsing(function (TextColumn $column): void {
            $column->toggleable();
        });

        return $table
            ->header($this->getHeading())
            ->poll('10s')
            ->striped()
            ->deferLoading()
            ->description('Manage all registered citizen accounts.')
            ->query(fn (): Builder => User::whereHas('roles', fn ($q) => $q->where('name', 'citizen')))
            ->columns([
                ImageColumn::make('profile_pic')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Profile</span>'))
                    ->circular()
                    ->size(42)
                    ->alignCenter()
                    ->getStateUsing(fn ($record) =>
                        $record->profile_pic
                            ? asset('storage/' . $record->profile_pic)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
                    ),

                TextColumn::make('name')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Full Name</span>'))
                    ->sortable()
                    ->searchable()
                    ->weight('semibold')
                    ->alignCenter(),

                TextColumn::make('email')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Email Address</span>'))
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),

                IconColumn::make('status')
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Status</span>'))
                    ->boolean()
                    ->alignCenter()
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
                    ->label(new HtmlString('<span class="text-[13px] font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">Date Joined</span>'))
                    ->dateTime('F d, Y • h:i A')
                    ->alignCenter()
                    ->sortable()
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

                Filter::make('joined_today')
                    ->label('Joined Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', now()->today())),
            ])
            ->actions([
    ActionGroup::make([
        ViewAction::make()
            ->label('View Details')
            ->color('info')
            ->icon('heroicon-o-eye')
            ->button()
            ->outlined()
            ->tooltip('View user information')
            ->infolist([
                Section::make('User Details')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                ImageEntry::make('profile_pic')
                                    ->label('Profile Picture')
                                    ->circular()
                                    ->getStateUsing(fn ($record) =>
                                        $record->profile_pic
                                            ? asset('storage/' . $record->profile_pic)
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
                                    ),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Full Name')
                                    ->icon('heroicon-o-user')
                                    ->weight('bold'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'online' => 'success',
                                        'away' => 'warning',
                                        'offline' => 'gray',
                                        default => 'secondary',
                                    })
                                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                                TextEntry::make('created_at')
                                    ->label('Joined On')
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime('F d, Y'),
                            ]),
                    ]),
            ]),

        EditAction::make()
            ->label('Edit User')
            ->color('warning')
            ->icon('heroicon-o-pencil-square')
            ->button()
            ->tooltip('Edit this user')
            ->outlined()
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
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'online' => 'Online',
                        'away' => 'Away',
                        'offline' => 'Offline',
                    ]),
            ]),

        DeleteAction::make()
            ->label('Delete User')
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->button()
            ->outlined()
            ->requiresConfirmation()
            ->modalHeading('Confirm Deletion')
            ->modalSubheading('Are you sure you want to delete this user? This action cannot be undone.')
            ->tooltip('Delete this user permanently'),
    ])
        ->color('gray')
        ->label('Actions')
        ->icon('heroicon-o-cog-8-tooth')
        ->tooltip('Manage user record')
        ->button(),
])
->bulkActions([
    BulkActionGroup::make([
        BulkAction::make('delete')
            ->label('Delete Selected')
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->modalHeading('Confirm Bulk Deletion')
            ->modalSubheading('Are you sure you want to delete all selected users?')
            ->action(fn (Collection $records) => $records->each->delete())
            ->successNotificationTitle('Selected users deleted successfully.')
            ->failureNotificationTitle(fn (int $successCount, int $totalCount): string =>
                $successCount
                    ? "{$successCount} of {$totalCount} users deleted."
                    : 'Failed to delete any users.'
            ),
    ])
        ->label('Bulk Actions')
        ->icon('heroicon-o-check-circle')
        ->color('gray'),
])

            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->paginated([5, 10, 25, 50, 100, 'all']);
    }
}
