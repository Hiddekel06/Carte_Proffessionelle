<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\CarteEvenement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoriqueCarteTest extends TestCase
{
    use RefreshDatabase;

    public function test_impression_et_remise_sont_enregistrees_avec_l_auteur(): void
    {
        $admin = User::factory()->create(['name' => 'Chef RH']);
        $agent = Agent::create(['matricule' => '111111A', 'prenom' => 'Awa', 'nom' => 'Ndiaye']);

        $this->actingAs($admin)->postJson(route('admin.agents.carte-imprimee', $agent))->assertOk();
        $this->actingAs($admin)->post(route('admin.agents.etat-carte'), ['action' => 'remise', 'agents' => [$agent->id]]);

        $this->assertSame(
            [CarteEvenement::IMPRIMEE, CarteEvenement::REMISE],
            CarteEvenement::orderBy('id')->pluck('action')->all()
        );
        $this->assertDatabaseHas('carte_evenements', [
            'agent_id' => $agent->id,
            'agent_matricule' => '111111A',
            'action' => CarteEvenement::REMISE,
            'user_nom' => 'Chef RH',
        ]);
    }

    public function test_remise_refusee_n_est_pas_enregistree(): void
    {
        $agent = Agent::create(['matricule' => '111111A', 'prenom' => 'Awa', 'nom' => 'Ndiaye']);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.agents.etat-carte'), ['action' => 'remise', 'agents' => [$agent->id]]);

        $this->assertSame(0, CarteEvenement::count());
    }

    public function test_historique_reste_lisible_apres_suppression_de_l_agent(): void
    {
        $admin = User::factory()->create();
        $agent = Agent::create(['matricule' => '111111A', 'prenom' => 'Awa', 'nom' => 'Ndiaye', 'carte_imprimee_le' => now()]);
        $this->actingAs($admin)->post(route('admin.agents.etat-carte'), ['action' => 'remise', 'agents' => [$agent->id]]);

        $agent->delete();

        $this->actingAs($admin)->get(route('admin.historique.index'))
            ->assertOk()
            ->assertSee('NDIAYE Awa')
            ->assertSee('fiche supprimée');
    }

    public function test_filtres_par_action_et_par_date(): void
    {
        $admin = User::factory()->create();
        $a = Agent::create(['matricule' => '111111A', 'prenom' => 'Awa', 'nom' => 'Ndiaye']);
        $b = Agent::create(['matricule' => '222222B', 'prenom' => 'Moussa', 'nom' => 'Fall']);

        CarteEvenement::forceCreate(['agent_id' => $a->id, 'agent_matricule' => '111111A', 'agent_nom' => 'NDIAYE Awa', 'action' => 'remise', 'created_at' => '2026-11-10 10:00:00']);
        CarteEvenement::forceCreate(['agent_id' => $b->id, 'agent_matricule' => '222222B', 'agent_nom' => 'FALL Moussa', 'action' => 'remise', 'created_at' => '2026-11-15 10:00:00']);
        CarteEvenement::forceCreate(['agent_id' => $b->id, 'agent_matricule' => '222222B', 'agent_nom' => 'FALL Moussa', 'action' => 'imprimee', 'created_at' => '2026-11-15 09:00:00']);

        $this->actingAs($admin)
            ->get(route('admin.historique.index', ['action' => 'remise', 'du' => '2026-11-15', 'au' => '2026-11-15']))
            ->assertOk()
            ->assertSee('FALL Moussa')
            ->assertDontSee('NDIAYE Awa')
            ->assertDontSee('09:00');
    }
}
