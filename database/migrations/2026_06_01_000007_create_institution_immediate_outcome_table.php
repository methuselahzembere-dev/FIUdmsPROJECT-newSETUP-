<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_immediate_outcome', function (Blueprint $table) {
            $table->id();
            
            // 🌟 ADDED: Your key relationship back to the reporting institutions table
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            
            // 🌟 FIXED: Kept ONLY the single, valid reference to your new outcomes table
            $table->foreignId('immediate_outcome_id')->constrained('effectiveness_immediate_outcomes')->cascadeOnDelete();
            
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->string('status', 50)->default('assigned')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique and indexed constraint mappings
            $table->unique(['institution_id', 'immediate_outcome_id'], 'institution_outcome_unique');
            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_immediate_outcome');
    }
};