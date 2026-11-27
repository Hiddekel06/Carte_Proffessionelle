<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Suivi de la carte professionnelle :
     *  - carte_imprimee_le null                       => « Non imprimée »
     *  - carte_imprimee_le rempli, carte_remise_le null => « Imprimée, à remettre »
     *  - carte_remise_le rempli                        => « Remise »
     */
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->timestamp('carte_imprimee_le')->nullable()->after('modele_carte');
            $table->timestamp('carte_remise_le')->nullable()->after('carte_imprimee_le');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['carte_imprimee_le', 'carte_remise_le']);
        });
    }
};
