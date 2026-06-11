<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('effectiveness_documents')) {
            return;
        }

        Schema::create('effectiveness_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effectiveness_sub_io_id')
                ->nullable()
                ->constrained('effectiveness_sub_immediate_outcomes')
                ->nullOnDelete();
            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('institutions')
                ->nullOnDelete();

            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('reporting_institution')->nullable();
            $table->string('status', 50)->default('logged');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('disk')->nullable();
            $table->date('date_logged')->nullable();
            $table->date('document_date')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('effectiveness_sub_io_id', 'eff_docs_sub_io_idx');
            $table->index('institution_id', 'eff_docs_institution_idx');
            $table->index('status', 'eff_docs_status_idx');
            $table->index('date_logged', 'eff_docs_date_logged_idx');
            $table->index('document_date', 'eff_docs_document_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effectiveness_documents');
    }
};