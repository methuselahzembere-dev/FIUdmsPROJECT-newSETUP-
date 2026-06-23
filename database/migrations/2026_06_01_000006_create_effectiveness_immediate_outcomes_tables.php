<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('effectiveness_immediate_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->unsignedTinyInteger('number')->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('effectiveness_sub_immediate_outcomes', function (Blueprint $table): void {
            $table->id();
        // 🌟 FIXED: Keep constrained pointing strictly to the table, and use an explicit short foreign key name string
    $table->foreignId('immediate_outcome_id')
        ->constrained('effectiveness_immediate_outcomes')
        ->cascadeOnDelete()
        ->index('eff_sub_io_parent_fk');
            $table->string('code', 20)->unique();
            $table->unsignedTinyInteger('main_number');
            $table->unsignedTinyInteger('sub_number');
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['immediate_outcome_id', 'sub_number'], 'effectiveness_sub_ios_parent_sub_unique');
            $table->index(['main_number', 'sub_number'], 'effectiveness_sub_ios_main_sub_index');
        });

        if (Schema::hasTable('effectiveness_documents')) {
            Schema::table('effectiveness_documents', function (Blueprint $table): void {
                if (! Schema::hasColumn('effectiveness_documents', 'effectiveness_sub_io_id')) {
                 $table->foreignId('effectiveness_sub_io_id')
                ->nullable()
                ->after('effectiveness_folder_id')
                ->constrained('effectiveness_sub_immediate_outcomes')
                ->nullOnDelete()
                ->index('eff_doc_sub_io_fk');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('effectiveness_documents') && Schema::hasColumn('effectiveness_documents', 'effectiveness_sub_io_id')) {
            Schema::table('effectiveness_documents', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('effectiveness_sub_io_id');
            });
        }

        Schema::dropIfExists('effectiveness_sub_immediate_outcomes');
        Schema::dropIfExists('effectiveness_immediate_outcomes');
    }
};
