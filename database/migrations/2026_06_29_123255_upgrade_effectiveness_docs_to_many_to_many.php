<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
    {
        // 1. Clean up the old One-to-Many architecture
        if (Schema::hasTable('effectiveness_documents') && Schema::hasColumn('effectiveness_documents', 'effectiveness_sub_io_id')) {
            Schema::table('effectiveness_documents', function (Blueprint $table) {
                $table->dropForeign(['effectiveness_sub_io_id']);
                $table->dropColumn('effectiveness_sub_io_id');
            });
        }

        // 2. Build the new Many-to-Many Pivot Table (ONLY if it doesn't already exist!)
        if (!Schema::hasTable('document_sub_io')) {
            Schema::create('document_sub_io', function (Blueprint $table) {
                $table->id();
                
                $table->foreignId('document_id')
                      ->constrained('documents')
                      ->cascadeOnDelete();
                      
                $table->foreignId('sub_io_id')
                      ->constrained('effectiveness_sub_immediate_outcomes')
                      ->cascadeOnDelete();
                      
                $table->timestamps();
                
                $table->unique(['document_id', 'sub_io_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sub_io');

        Schema::table('effectiveness_documents', function (Blueprint $table) {
            $table->foreignId('effectiveness_sub_io_id')
                  ->nullable()
                  ->after('effectiveness_folder_id')
                  ->constrained('effectiveness_sub_immediate_outcomes')
                  ->nullOnDelete()
                  ->index('eff_doc_sub_io_fk');
        });
    }
};