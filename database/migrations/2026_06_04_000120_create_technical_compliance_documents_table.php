<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('technical_compliance_documents', function (Blueprint $table) {
            $table->id();
            
            // 🌟 FIXED: Point to unified 'folders' table and specify short custom constraint name to prevent length errors
            $table->foreignId('folder_id')
                  ->constrained('folders')
                  ->cascadeOnDelete()
                  ->name('tc_docs_folder_id_fk');

            // 🌟 FIXED: Links to your multi-tenant institutions table
            $table->foreignId('institution_id')
                  ->constrained('institutions')
                  ->cascadeOnDelete()
                  ->name('tc_docs_institution_id_fk');

            // Auditing & Review Tracking Context Nodes
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // File Information Payload
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('stored_path');
            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            
            // Status Flags & Chronological Tracing Indicators
            $table->string('status')->default('submitted')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            // Compound Optimization Indexing - Shortened key string
            $table->index(['folder_id', 'institution_id'], 'tc_docs_folder_inst_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_compliance_documents');
    }
};