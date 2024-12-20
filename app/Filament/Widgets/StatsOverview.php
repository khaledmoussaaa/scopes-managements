<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Fetch daily counts for users, employees, departments, and tasks
        $userCounts = $this->getDailyCounts(User::class);
        $employeeCounts = $this->getDailyCounts(Employee::class);
        $departmentCounts = $this->getDailyCounts(Department::class);
        $taskCounts = $this->getDailyCounts(Task::class);

        return [
            Stat::make('Total Users', User::count())
                ->description('The total count of registered users')
                ->descriptionIcon('heroicon-o-users')
                ->chart($userCounts)
                ->color('success'),

            Stat::make('Total Employees', Employee::count())
                ->description('The total count of employees')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart($employeeCounts)
                ->color('primary'),

            Stat::make('Total Departments', Department::count())
                ->description('The total count of departments')
                ->descriptionIcon('heroicon-o-building-library')
                ->chart($departmentCounts)
                ->color('info'),

            Stat::make('Total Tasks', Task::count())
                ->description('The total count of tasks')
                ->descriptionIcon('heroicon-o-queue-list')
                ->chart($taskCounts)
                ->color('gray'),
        ];
    }

    /**
     * Get daily counts for a given model over the last 7 days.
     *
     * @param string $modelClass
     * @return array
     */
    protected function getDailyCounts(string $modelClass): array
    {
        $counts = [];

        // Loop through the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();

            // Count records created on the given day
            $counts[] = $modelClass::whereDate('created_at', $date)->count();
        }

        return $counts;
    }
}