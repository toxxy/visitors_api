<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'description',
        'site_id',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
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
