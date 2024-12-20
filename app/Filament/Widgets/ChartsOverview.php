<?php

namespace App\Filament\Widgets;

use App\Models\SubTask;
use Filament\Widgets\ChartWidget;

class ChartsOverview extends ChartWidget
{
    protected static ?string $heading = 'Task Status Overview';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '250px';

    /**
     * Get the chart data dynamically based on task status.
     */
    protected function getData(): array
    {
        // Fetch the counts of tasks grouped by status
        $taskCounts = $this->getTaskCounts();

        return [
            'datasets' => [
                [
                    'label' => 'Tasks Status',
                    'data' => [
                        $taskCounts['not_started'] ?? 0,
                        $taskCounts['in_progress'] ?? 0,
                        $taskCounts['review'] ?? 0,
                        $taskCounts['done'] ?? 0,
                    ],
                    'backgroundColor' => ['rgb(161, 161, 144, 0.5)', 'rgb(97, 147, 255, 0.5)', 'rgb(153, 105, 201, 0.5)', 'rgb(48, 145, 83, 0.5)'], // Custom colors for each statu
                    'borderColor' => ['#b7b7c6', '#60a5fa', '#c084fc', '#cbf6db'],
                ],
            ],
            'labels' => ['Not Started', 'In Progress', 'Review', 'Done'],
        ];
    }

    /**
     * Get the type of chart.
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Fetch task counts grouped by status.
     */
    protected function getTaskCounts(): array
    {
        return SubTask::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status') // Returns ['not_started' => 5, 'in_progress' => 10, ...]
            ->toArray();
    }
}
