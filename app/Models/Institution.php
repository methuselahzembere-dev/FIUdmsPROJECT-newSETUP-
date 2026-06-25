<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'portal_title',
        'contact_email',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'reporting_institution_id');
    }

  /**
     * The Effectiveness Immediate Outcomes assigned to this institution.
     */
    public function effectivenessImmediateOutcomes()
    {
        return $this->belongsToMany(EffectivenessImmediateOutcome::class, 'effectiveness_immediate_outcome_institution');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
