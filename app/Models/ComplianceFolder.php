<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplianceFolder extends Model
{
    use HasFactory;

    public const TRACK_TECHNICAL = 'technical';
    public const TRACK_EFFECTIVENESS = 'effectiveness';

    protected $fillable = [
        'name',
        'slug',
        'track',
        'description',
        'immediate_outcome_id',
        'created_by',
    ];

    public function immediateOutcome(): BelongsTo
    {
        return $this->belongsTo(ImmediateOutcome::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'folder_id');
    }
}
