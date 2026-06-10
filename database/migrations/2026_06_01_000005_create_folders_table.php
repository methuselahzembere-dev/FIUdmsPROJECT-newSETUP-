<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            
            // Core track link (Connects to Technical Compliance vs Effectiveness tracks)
            $table->foreignId('compliance_track_id')->constrained('compliance_tracks')->cascadeOnDelete();
            
            // Context Isolation: If null, it is global. If assigned, it belongs to that specific institution.
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            
            // Self-referencing tree support to allow deep nested child folders
            $table->foreignId('parent_id')->nullable()->constrained('folders')->nullOnDelete();
            
            // Staff auditing trace tracking who created this custom workspace folder
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Folder details
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            
            // Operational indicators and visibility rules
            $table->boolean('is_default')->default(false)->index(); 
            $table->boolean('is_visible_to_institutions')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            
            $table->timestamps();
            $table->softDeletes();

            // Custom named unique index to secure null fields and bypass MySQL length bounds
            $table->unique(['compliance_track_id', 'institution_id', 'parent_id', 'slug'], 'folders_unique_context_slug');
            $table->index(['compliance_track_id', 'institution_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};