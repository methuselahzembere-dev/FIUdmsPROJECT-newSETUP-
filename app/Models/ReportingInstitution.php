<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportingInstitution extends Model
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

    public function assignedImmediateOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(ImmediateOutcome::class, 'institution_immediate_outcome')
            ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
