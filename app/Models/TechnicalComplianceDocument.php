<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TechnicalComplianceDocument extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_REVISION_REQUESTED = 'revision_requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'technical_compliance_folder_id',
        'reporting_institution_id',
        'uploaded_by',
        'reviewed_by',
        'title',
        'description',
        'stored_path',
        'original_filename',
        'mime_type',
        'status',
        'submitted_at',
        'reviewed_at',
        'archived_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(TechnicalComplianceFolder::class, 'technical_compliance_folder_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(ReportingInstitution::class, 'reporting_institution_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(TechnicalComplianceDocumentRevision::class, 'technical_compliance_document_id');
    }

    public function canBeEditedByInstitution(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISION_REQUESTED], true);
    }
}
