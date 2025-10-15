<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'company',
        'purpose',
        'department_id',
        'site_id',
        'scheduled_at',
        'arrived_at',
        'departed_at',
        'status',
        'updated_by',
        'notes',
        'is_invalid',
        'invalid_reason',
        'checked_in_at',
        'checked_out_at',
        'check_in_count',
        'check_out_count'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'arrived_at' => 'datetime',
        'departed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'is_invalid' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
