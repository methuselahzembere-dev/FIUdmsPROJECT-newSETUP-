<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This creates the central Document node that links to your 
        // pivot tables (folders, institutions, outcomes)
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('workspace_track'); // 'technical' or 'effectiveness'
            $table->string('visibility_scope'); // 'shared' or 'internal'
            $table->string('title');
            $table->string('name')->nullable();
            $table->string('reporting_institution');
            $table->date('date_logged');
            $table->date('document_date')->nullable();
            $table->string('status', 50)->index();
            $table->text('remarks')->nullable();
            $table->string('file_path', 1000);
            $table->string('external_file_name')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};