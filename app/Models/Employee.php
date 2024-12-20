<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Employee extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'salary',
        'experties',
        'user_id',
        'department_id',
    ];

    // ======================== Tenancy ======================== //
    protected static function booted(): void
    {
        static::addGlobalScope('employeeDepartment', function (Builder $query) {
            if (auth()->check() && auth()->user()->admin_department) {
                $query->where('department_id', auth()->user()->admin_department->id);
            }
        });
    }

    // ======================== Relations ======================== //
    /**
     * Get the employee the user belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the department the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
