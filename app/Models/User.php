<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'site_id',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the site that the user belongs to.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user has permission to view all sites
     */
    public function canViewAllSites()
    {
        return in_array($this->role, ['admin_master', 'security']);
    }

    /**
     * Check if user has permission to view site
     */
    public function canViewSite($siteId)
    {
        if ($this->canViewAllSites()) {
            return true;
        }
        
        return $this->role === 'admin_site' && $this->site_id == $siteId;
    }

    /**
     * Check if user has permission to view department
     */
    public function canViewDepartment($departmentId)
    {
        if ($this->canViewAllSites()) {
            return true;
        }
        
        if ($this->role === 'admin_site') {
            // Admin de sitio puede ver todos los departamentos de su sitio
            $department = Department::find($departmentId);
            return $department && $department->site_id == $this->site_id;
        }
        
        return $this->role === 'manager' && $this->department_id == $departmentId;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
