<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EffectivenessSubImmediateOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'immediate_outcome_id',
        'code',
        'main_number',
        'sub_number',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'immediate_outcome_id' => 'integer',
        'main_number' => 'integer',
        'sub_number' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function immediateOutcome(): BelongsTo
    {
        return $this->belongsTo(EffectivenessImmediateOutcome::class, 'immediate_outcome_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EffectivenessDocument::class, 'effectiveness_sub_io_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}