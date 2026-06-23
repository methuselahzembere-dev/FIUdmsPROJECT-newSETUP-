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
        Schema::create('folder_institution_visibility', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys to link folders and institutions
            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            
            $table->timestamps();

            // Prevent the same institution from being assigned to the same folder twice
            $table->unique(['folder_id', 'institution_id'], 'folder_inst_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder_institution_visibility');
    }
};