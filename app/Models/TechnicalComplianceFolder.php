<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TechnicalComplianceFolder extends Model
{
    use HasFactory;

    public const TRACK = 'technical';

    public const DEFAULT_FOLDERS = [
        'Acts and Statutory Instruments',
        'Case Studies',
        'Enforcement Means',
        'Recommendations',
        'Regulations',
        'Risk Assessments and Strategies',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $folder): void {
            $folder->slug ??= Str::slug($folder->name);
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TechnicalComplianceDocument::class, 'technical_compliance_folder_id');
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(ReportingInstitution::class, 'reporting_institution_technical_compliance_folder')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
