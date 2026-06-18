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
        Schema::table('technical_compliance_documents', function (Blueprint $table) {
            // 'shared'  = Normal cross-tenant visibility matrix mapping
            // 'internal' = Hard sandboxed to internal FIU staff profiles only
            $table->string('visibility_scope', 20)->default('shared')->after('status')->index();
        });

        Schema::table('effectiveness_documents', function (Blueprint $table) {
            $table->string('visibility_scope', 20)->default('shared')->after('status')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technical_compliance_documents', function (Blueprint $table) {
            $table->dropColumn('visibility_scope');
        });

        Schema::table('effectiveness_documents', function (Blueprint $table) {
            $table->dropColumn('visibility_scope');
        });
    }
};