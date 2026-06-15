<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EffectivenessDocument extends Model
{
    use HasFactory;

    protected $table = 'effectiveness_documents';

    protected $fillable = [
        'effectiveness_sub_io_id',
        'institution_id',
        'title',
        'name',
        'reporting_institution',
        'status',
        'file_name',
        'file_path',
        'disk',
        'date_logged',
        'document_date',
        'submitted_at',
        'approved_at',
        'remarks',
        'meta',
        'updated_by'
    ];

    protected $casts = [
        'effectiveness_sub_io_id' => 'integer',
        'institution_id' => 'integer',
        'date_logged' => 'date',
        'document_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'meta' => 'array',
    ];

    public function subImmediateOutcome(): BelongsTo
    {
        return $this->belongsTo(EffectivenessSubImmediateOutcome::class, 'effectiveness_sub_io_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship tracking the user who uploaded/created this document.
     */
    public function creator(): BelongsTo
    {
        // Adjust 'user_id' if your original column uses 'created_by'
        return $this->belongsTo(User::class, 'user_id'); 
    }
}