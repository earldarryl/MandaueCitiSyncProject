<?php

namespace App\Livewire\Admin\Tables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CitizensTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
{
    $this->setPrimaryKey('id')
        ->setDefaultSort('created_at', 'desc')
        ->setPerPageAccepted([10, 25, 50, 100]);

    // Enable placeholder loader
    $this->setLoadingPlaceholderEnabled()
    ->setLoadingPlaceholderBlade('components.table.loading');

    // Search customization
    $this->setSearchPlaceholder('Enter Search Term');
    $this->setSearchIcon('heroicon-m-magnifying-glass');
    $this->setSearchIconAttributes([
        'class' => 'h-4 w-4 text-black dark:text-white shadow-none',
    ]);

    // ✅ Center all cells (contents)
    $this->setTdAttributes(fn(Column $column, $row, $columnIndex, $rowIndex) => [
        'default' => true,
        'class'   => 'text-center align-middle',
    ]);
}




    /**
     * Only show users with the "citizen" role.
     */
    public function builder(): Builder
    {
        return User::query()->role('citizen'); // ✅ type matches Builder
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable()
                ->searchable(),

            Column::make("Name", "name")
                ->sortable()
                ->searchable(),

            Column::make("Email", "email")
                ->sortable()
                ->searchable(),

            Column::make("Profile Pic", "profile_pic")
                ->format(fn ($value, $row) => $value
                    ? '<img src="'.asset('storage/'.$value).'" class="h-10 w-10 rounded-full object-cover">'
                    : '<span class="text-gray-400">N/A</span>'
                )
                ->html(),

            Column::make("Contact", "contact")
                ->sortable()
                ->searchable(),

            Column::make("Agreed Terms", "agreed_terms")
                ->format(fn ($value) => $value ? '✅ Yes' : '❌ No'),

            Column::make("Terms Version", "terms_version")
                ->sortable(),

            Column::make("Agreed At", "agreed_at")
                ->sortable(),

            Column::make("Created At", "created_at")
                ->sortable(),

            Column::make("Updated At", "updated_at")
                ->sortable(),
        ];
    }
}
