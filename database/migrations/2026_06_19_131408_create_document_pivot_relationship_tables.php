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
        // 1. Pivot Bridge for Technical Compliance Folders
        Schema::create('document_technical_folder', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            // 🌟 FIXED: Changed 'technical_compliance_folders' to match your actual master table 'folders'
            $table->foreignId('folder_id')->constrained('folders')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Pivot Bridge for Effectiveness Sub-Immediate Outcomes
        Schema::create('document_sub_io', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('sub_io_id')->constrained('effectiveness_sub_immediate_outcomes')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Pivot Bridge for Multi-Tenant Shared Institution Access Mappings
        Schema::create('document_institution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Pivot Bridge for Sandboxed Private Internal User Access Group Targets
        Schema::create('document_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_user');
        Schema::dropIfExists('document_institution');
        Schema::dropIfExists('document_sub_io');
        Schema::dropIfExists('document_technical_folder');
    }
};