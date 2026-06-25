<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EffectivenessImmediateOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'number',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'number' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];


    protected static function booted(): void
    {
        // Bind the tenant immediate outcome isolation scope natively
        static::addGlobalScope(new \App\Models\Scopes\TenantImmediateOutcomeScope);
    }

    public function subOutcomes(): HasMany
    {
        return $this->hasMany(EffectivenessSubImmediateOutcome::class, 'immediate_outcome_id')
            ->orderBy('sort_order')
            ->orderBy('code');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * The institutions that have access to this Immediate Outcome.
     */
    public function institutions()
    {
        return $this->belongsToMany(Institution::class, 'effectiveness_immediate_outcome_institution');
    }
}