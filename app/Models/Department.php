<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Department extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'admin_department_id',
    ];



    // ======================== Relations ======================== //
    /**
     * Get the admin of the department.
     */
    public function admin_department()
    {
        return $this->belongsTo(User::class, 'admin_department_id');
    }

    /**
     * Get the employee of the department.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function department_employees()
    {
        return $this->hasMany(Employee::class);
    }
}
