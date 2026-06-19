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
        Schema::create('effectiveness_immediate_outcome_institution', function (Blueprint $table): void {
            $table->id();
            
            //  Foreign Key binding to your parent Effectiveness Outcomes table
            $table->foreignId('effectiveness_immediate_outcome_id')
                  ->constrained('effectiveness_immediate_outcomes')
                  ->cascadeOnDelete()
                  ->index('eff_io_inst_parent_fk');
                  
            //  Foreign Key binding to your multi-tenant Institutions table
            $table->foreignId('institution_id')
                  ->constrained('institutions')
                  ->cascadeOnDelete()
                  ->index('eff_io_inst_tenant_fk');

            //  COMPOUND INTEGRITY INDEX: Prevents duplicate assignments at the DB layer
            $table->unique(
                ['effectiveness_immediate_outcome_id', 'institution_id'], 
                'eff_io_inst_unique_matrix'
            );
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effectiveness_immediate_outcome_institution');
    }
};