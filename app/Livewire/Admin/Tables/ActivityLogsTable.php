<?php

namespace App\Livewire\Admin\Tables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\ActivityLog;

class ActivityLogsTable extends DataTableComponent
{
    protected $model = ActivityLog::class;

    public function configure(): void
    {
        $this->setPrimaryKey('activity_log_id')
            ->setDefaultSort('timestamp', 'desc')
            ->setColumnSelectStatus(true)

            // 🔹 Make rows clickable
            ->setTableRowUrl(fn($row) => route('dashboard', $row->activity_log_id))
            ->setTableRowUrlTarget(fn($row) => '_self');

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



    public function columns(): array
    {
        return [
            Column::make("Activity Log ID", "activity_log_id")
                ->sortable(),

            Column::make("User", "user.name")
                ->sortable()
                ->searchable(),

            Column::make("Role", "role.name")
                ->sortable()
                ->searchable(),

            Column::make("Action", "action")
                ->sortable()
                ->searchable()
                ->unclickable(),


            Column::make("Timestamp", "timestamp")
                ->sortable()
                ->searchable(),

            Column::make("IP Address", "ip_address")
                ->sortable()
                ->searchable(),

            Column::make("Device Info", "device_info")
                ->sortable()
                ->searchable(),

            Column::make("Created At", "created_at")
                ->sortable(),

            Column::make("Updated At", "updated_at")
                ->sortable(),
        ];
    }
}
