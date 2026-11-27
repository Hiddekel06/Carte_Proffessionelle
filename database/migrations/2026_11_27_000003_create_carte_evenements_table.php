<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historique (journal) des actions sur les cartes professionnelles.
     * Le nom et le matricule de l'agent sont recopiés : l'historique reste
     * lisible même si la fiche de l'agent est supprimée plus tard.
     */
    public function up(): void
    {
        Schema::create('carte_evenements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->string('agent_matricule', 20);
            $table->string('agent_nom');
            $table->string('action', 30); // imprimee, remise, annulation_remise, reinitialisee
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_nom')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carte_evenements');
    }
};
