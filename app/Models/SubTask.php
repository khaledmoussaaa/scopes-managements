<?php

namespace App\Models;

use App\TaskStatus;

class SubTask extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'status',
        'priority',
        'task_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'status' => TaskStatus::class
    ];

    // ======================== Relations ======================== //
    /**
     * Get the parent task.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
