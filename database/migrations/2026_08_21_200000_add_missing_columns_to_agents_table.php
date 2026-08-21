<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'structure_affectation')) {
                $table->string('structure_affectation')->nullable()->after('telephone');
            }
            if (!Schema::hasColumn('agents', 'service')) {
                $table->string('service')->nullable()->after('structure_affectation');
            }
            if (!Schema::hasColumn('agents', 'fonction')) {
                $table->string('fonction')->nullable()->after('service');
            }
            if (!Schema::hasColumn('agents', 'poste')) {
                $table->string('poste')->nullable()->after('fonction');
            }
            if (!Schema::hasColumn('agents', 'date_integration_fp')) {
                $table->date('date_integration_fp')->nullable()->after('poste');
            }
        });
    }

    public function down(): void
    {
        // Pas de suppression destructive automatique
    }
};
