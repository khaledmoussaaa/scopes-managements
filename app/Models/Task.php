<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'priority',
    ];

    // ======================== Tenancy ======================== //
    protected static function booted(): void
    {
        static::addGlobalScope('departmentTasks', function (Builder $query) {
            $user = auth()->user();

            if (!auth()->check()) {
                return;
            }

            // Scenario 3: Super Admin or users with permission get all tasks
            if ($user->hasRole('super_admin') || $user->can('showAllTasks')) {
                return;
            }

            // Scenario 1: Admin Department - Get tasks related to their department
            if ($user->admin_department) {
                $query->whereHas('departments', function (Builder $q) use ($user) {
                    $q->where('department_id', $user->admin_department->id);
                });
            }

            // Scenario 2: Employees - Get tasks explicitly assigned to the employee
            if ($user->employee) {
                $query->orWhereHas('admins', function (Builder $q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            // NEW Scenario: Users with tasks assigned, but not employees
            $query->orWhereHas('admins', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        });
    }

    // ======================== Relations ======================== //

    /**
     * Subtasks for the current task.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(SubTask::class);
    }

    /**
     * Departments associated with the task.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'task_assignees', 'task_id', 'department_id');
    }

    /**
     * Users (admins) associated with the task.
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignees', 'task_id', 'user_id');
    }
}
