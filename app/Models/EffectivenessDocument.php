<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EffectivenessDocument extends Model
{
    use HasFactory;

    /**
     * Change this only if your real table name is different.
     */
    protected $table = 'effectiveness_documents';

    protected $guarded = [];

    public function subImmediateOutcome(): BelongsTo
    {
        return $this->belongsTo(EffectivenessSubImmediateOutcome::class, 'effectiveness_sub_io_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}