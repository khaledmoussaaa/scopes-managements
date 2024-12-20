<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use Filament\Widgets\ChartWidget;

class DepartmentChart extends ChartWidget
{
    protected static ?string $heading = 'Employees per Department';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // Fetch the counts of employees grouped by department
        $departmentEmployees = $this->getDepartmentEmployeeCounts();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Employees',
                    'data' => array_values($departmentEmployees), // Employee counts as data
                ],
            ],
            'labels' => array_keys($departmentEmployees), // Department names as labels
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart for better visualization
    }

    /**
     * Fetch employee counts grouped by department.
     */
    protected function getDepartmentEmployeeCounts(): array
    {
        // Fetch departments and count employees using a relationship
        return Department::withCount('department_employees') // Assumes "employees" is a relationship in the Department model
            ->pluck('department_employees_count', 'name') // Returns ['Department A' => 10, 'Department B' => 15]
            ->toArray();
    }
}