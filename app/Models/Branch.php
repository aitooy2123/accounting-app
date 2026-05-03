<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'manager',
        'is_active',
        'copany_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

      public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the customers for the branch.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope a query to only include active branches.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive branches.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
