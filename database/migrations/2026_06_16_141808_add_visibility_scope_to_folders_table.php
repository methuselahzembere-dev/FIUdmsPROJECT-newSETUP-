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
        Schema::table('folders', function (Blueprint $table) {
            // 'shared'      = Normal behavior (visible to assigned multi-tenant institutions)
            // 'fiu-private' = Strict Isolation (completely hidden from external users, visible to FIU only)
            $table->string('visibility_scope', 20)
                ->default('shared')
                ->after('compliance_track_id')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropColumn('visibility_scope');
        });
    }
};