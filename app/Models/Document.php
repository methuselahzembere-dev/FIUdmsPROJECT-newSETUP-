<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Scopes\TenantDocumentScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The database table name associated with this unified model.
     *
     * @var string
     */
    protected $table = 'documents';

    /**
     * The attributes that are mass-assignable during payload persistence.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workspace_track',       // 'technical' or 'effectiveness'
        'visibility_scope',      // 'shared' or 'internal'
        'title',
        'name',                  // Internal document systemic code/tag
        'reporting_institution', // Free-text source label or fallback string
        'date_logged',
        'document_date',
        'status',                // 'submitted', 'under-review', etc.
        'remarks',               // Storage text area for audit logs and review notes
        'file_path',             // Location pointing inside your secure private storage disk
        'external_file_name',
        'user_id',               // The internal staff manager node that uploaded/approved the document
        'institution_id',        // Keeping legacy single-institution tracking compatibility
        'folder_id',             // Keeping legacy folder tracking compatibility if required
    ];

    /**
     * The attributes that should be cast to native PHP data types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_logged'   => 'date',
        'document_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Core Direct One-To-Many Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The internal staff manager or review officer who logged/owns this document node.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Legacy single institution relationship link.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }


    // A document belongs to one specific folder
    public function folder()
    {
        return $this->belongsTo(\App\Models\Folder::class, 'folder_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Multi-Tenant & Workflow Pivot Access Networks (Many-To-Many)
    |--------------------------------------------------------------------------
    */

    /**
     * Workspace [Technical Compliance]: Target folders assigned to this record execution node.
     */
    public function technicalFolders(): BelongsToMany
    {
        return $this->belongsToMany(
            TechnicalComplianceFolder::class, 
            'document_technical_folder', // Your many-to-many intermediate bridge table
            'document_id', 
            'folder_id'
        )->withTimestamps();
    }

    /**
     * Workspace [Effectiveness Outcomes]: Sub-IO constraints linked to this record execution node.
     */
    public function subOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(
            EffectivenessSubImmediateOutcome::class, 
            'document_sub_io', // Your many-to-many intermediate bridge table
            'document_id', 
            'sub_io_id'
        )->withTimestamps();
    }

    /**
     * Access Control [Shared Scope]: Multi-tenant tenant permissions network directory mapping.
     */
 // A document can be visible to MANY institutions via the pivot table
    public function institutions()
    {
        return $this->belongsToMany(
            \App\Models\Institution::class, 
            'document_institution_visibility', // The pivot table we created
            'document_id',
            'institution_id'
        );
    }

    /**
     * Access Control [Internal Scope]: Target internal system users assigned to sandboxed visibility groups.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, 
            'document_user', 
            'document_id', 
            'user_id'
        )->withTimestamps();
    }
}