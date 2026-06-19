<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TechnicalComplianceFolder extends Model
{
    use HasFactory, SoftDeletes; 
    protected $guarded = [];

    /**
     *  FORCE TABLE ALIGNMENT
     * Overrides Eloquent's plural snake-case guessing so this model 
     * targets your unified, multi-tenant 'folders' table precisely.
     */
    protected $table = 'folders';

    public const TRACK = 'technical';

    public const DEFAULT_FOLDERS = [
        'Acts and Statutory Instruments',
        'Case Studies',
        'Enforcement Means',
        'Recommendations',
        'Regulations',
        'Risk Assessments and Strategies',
    ];

    /**
     *  UPDATED FILLABLE ATTRIBUTES
     * Expanded to allow structural context insertion (Track, Tenant isolation, and Parent Trees).
     */
    protected $fillable = [
        'compliance_track_id',
        'institution_id',
        'parent_id',
        'created_by',
        'name',
        'visibility_scope',
        'slug',
        'description',
        'is_default',
        'is_visible_to_institutions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_visible_to_institutions' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

protected static function booted(): void
    {
        //  1. Wire up the multi-tenant isolation query guard matrix natively
        static::addGlobalScope(new \App\Models\Scopes\TenantComplianceScope);
        static::creating(function (self $folder): void {
            $folder->slug ??= \Illuminate\Support\Str::slug($folder->name);
            
            // BACKSTOP FORCE-BIND: Automatically enforce sorting fallback if empty
            $folder->sort_order ??= 0;
        });
    }
    /**
     * Auditing trace tracking the staff member who spawned this folder node.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * 🌟 UPDATED SELF-REFERENCING DOCUMENTS / CHILD RELATIONSHIP
     * Since files are stored in the same tree pattern using 'parent_id',
     * this counts or collects nested item records flawlessly.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(TechnicalComplianceDocument::class, 'folder_id');
    }

    /**
     * 🌟 MULTI-TENANT CONTEXT ISOLATION
     * Connects this folder container directly to its designated owning institution.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    /**
     * 🌟 BACKWARDS COMPATIBILITY BLADE WRAPPER
     * Keeps your index.blade.php from breaking if it calls the plural 'institutions' property.
     * Since a folder row has an individual 'institution_id', it maps cleanly to a singular relation.
     */
    public function institutions(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    /**
     * Global Scope filter to drop inactive file nodes out of the working view trees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}