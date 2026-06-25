<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * A folder contains many documents.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_id');
    }

    /**
     * If folders belong to a specific institution 
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}