<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Grievance;
use App\Models\Assignment;

class DashboardWidgets extends StatsOverviewWidget
{
    // Controls grid columns (responsive)
    protected int|array|null $columns = 2;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $onlineUsers = User::whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();

        $totalGrievances = Grievance::count();
        $pendingGrievances = Grievance::where('grievance_status', 'pending')->count();
        $resolvedGrievances = Grievance::where('grievance_status', 'resolved')->count();

        $totalAssignments = Assignment::count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description("{$onlineUsers} online now")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 10, 12, 9, 14, 18, $totalUsers]),

            Stat::make('Grievances', $totalGrievances)
                ->description("{$pendingGrievances} pending / {$resolvedGrievances} resolved")
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($pendingGrievances > 0 ? 'danger' : 'success')
                ->chart([$pendingGrievances, $resolvedGrievances, $totalGrievances]),

            Stat::make('Assignments', $totalAssignments)
                ->description('Distributed to HR liaisons')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info')
                ->chart([1, 2, 4, 8, $totalAssignments]),

            Stat::make('System Activity', $onlineUsers)
                ->description('Active in last 5 minutes')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($onlineUsers > 0 ? 'success' : 'secondary')
                ->chart([2, 3, 5, $onlineUsers]),
        ];
    }
}
