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
    'workspace_track',
    'visibility_scope',
    'title',
    'name',
    'reporting_institution',
    'date_logged',
    'document_date',
    'status',
    'remarks',
    'file_path',
    'external_file_name',
    'user_id'
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
public function subImmediateOutcomes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\EffectivenessSubImmediateOutcome::class,
            'document_sub_io', // The new pivot table
            'document_id',     // The foreign key for the document
            'sub_io_id'        // The foreign key for the Sub-IO
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
        )->withPivot('workspace_track');
    }


    /**
     * Get the user who uploaded the document.
     */
    public function uploader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // We use 'uploader' as the alias, but tell Laravel to look for 'user_id'
        return $this->belongsTo(\App\Models\User::class, 'user_id');
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
        )->withPivot('workspace_track')->withTimestamps();
    }
}