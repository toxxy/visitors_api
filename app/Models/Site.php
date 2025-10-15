<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'name',
        'location', 
        'address',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
