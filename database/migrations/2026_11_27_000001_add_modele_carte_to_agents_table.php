<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // null = automatique (déduit de la fonction / du poste), sinon 'standard' ou 'inspecteur'
            $table->string('modele_carte', 20)->nullable()->after('date_integration_fp');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('modele_carte');
        });
    }
};
