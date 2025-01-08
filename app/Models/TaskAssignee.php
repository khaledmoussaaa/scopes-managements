<?php

namespace App\Models;

class TaskAssignee extends BaseModel
{
    protected $fillable = [
        'task_id',
        'user_id',
        'department_id',
    ];
}
