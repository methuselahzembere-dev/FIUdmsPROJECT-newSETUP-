<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('technical_compliance_document_revisions', function (Blueprint $table) {
            $table->id();
            
            // 🌟 FIXED: Target the correct parent document table and explicitly use a short constraint key name
            $table->foreignId('technical_compliance_document_id')
                  ->constrained('technical_compliance_documents')
                  ->cascadeOnDelete()
                  ->name('tc_revisions_doc_id_fk');

            // Audit Trace Metadata
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            
            // Interaction Node Fields
            $table->text('comment');
            $table->string('status')->default('revision_requested');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_compliance_document_revisions');
    }
};