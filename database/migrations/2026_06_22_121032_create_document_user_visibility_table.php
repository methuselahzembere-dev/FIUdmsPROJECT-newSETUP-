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
        Schema::create('document_user_visibility', function (Blueprint $table) {
            $table->id();
            
            // Tracks whether this relates to a technical or effectiveness document
            $table->string('workspace_track'); 
            
            // The ID of the document (polymorphic, so we don't use a strict foreign key here)
            $table->unsignedBigInteger('document_id'); 
            
            // The ID of the FIU Reviewer/Admin
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();

            // This ensures we don't accidentally assign the same user to the same document twice
            $table->unique(['workspace_track', 'document_id', 'user_id'], 'doc_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_user_visibility');
    }
};