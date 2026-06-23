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
        Schema::create('document_institution_visibility', function (Blueprint $table) {
            $table->id();
            
            // Tracks whether this relates to a technical or effectiveness document
            $table->string('workspace_track'); 
            
            // The ID of the document (polymorphic)
            $table->unsignedBigInteger('document_id'); 
            
            // The ID of the target institution
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            
            $table->timestamps();

            // This ensures we don't accidentally assign the same institution to the same document twice
            $table->unique(['workspace_track', 'document_id', 'institution_id'], 'doc_inst_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_institution_visibility');
    }
};