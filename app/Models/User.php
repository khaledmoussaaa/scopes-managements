<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Casts\DateTimeCasting;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use TomatoPHP\FilamentMediaManager\Traits\InteractsWithMediaFolders;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPanelShield, InteractsWithMediaFolders;

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@gmail.com');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'birth_date',
        'hired_date',
        'position',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => DateTimeCasting::class,
            'updated_at' => DateTimeCasting::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ======================== Relations ======================== //
    /**
     * Get the user the employee belongs to.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Get the department the user belongs to.
     */
    public function admin_department()
    {
        return $this->hasOne(Department::class, 'admin_department_id');
    }

    // /**
    //  * Get the tasks assigned to the user.
    //  */
    // public function tasks()
    // {
    //     return $this->belongsToMany(Task::class, 'task_assignees')->withTimestamps();
    // }
}
