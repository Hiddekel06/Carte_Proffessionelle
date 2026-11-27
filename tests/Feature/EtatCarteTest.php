<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtatCarteTest extends TestCase
{
    use RefreshDatabase;

    private function agent(string $matricule, array $attrs = []): Agent
    {
        return Agent::create(array_merge([
            'matricule' => $matricule,
            'prenom' => 'Awa',
            'nom' => 'Ndiaye',
        ], $attrs));
    }

    public function test_nouvel_agent_est_non_imprime(): void
    {
        $this->assertSame(Agent::ETAT_NON_IMPRIMEE, $this->agent('111111A')->etat_carte);
    }

    public function test_impression_depuis_la_page_carte_passe_en_a_remettre(): void
    {
        $agent = $this->agent('111111A');

        $this->actingAs(User::factory()->create())
            ->postJson(route('admin.agents.carte-imprimee', $agent))
            ->assertOk()
            ->assertJson(['etat' => Agent::ETAT_A_REMETTRE]);

        $this->assertSame(Agent::ETAT_A_REMETTRE, $agent->fresh()->etat_carte);
    }

    public function test_impossible_de_remettre_une_carte_non_imprimee(): void
    {
        $nonImprimee = $this->agent('111111A');
        $imprimee = $this->agent('222222B', ['carte_imprimee_le' => now()]);

        $this->actingAs(User::factory()->create())
            ->from(route('admin.agents.index'))
            ->post(route('admin.agents.etat-carte'), [
                'action' => 'remise',
                'agents' => [$nonImprimee->id, $imprimee->id],
            ])
            ->assertRedirect(route('admin.agents.index'))
            ->assertSessionHas('error')
            ->assertSessionHas('success');

        $this->assertSame(Agent::ETAT_NON_IMPRIMEE, $nonImprimee->fresh()->etat_carte);
        $this->assertSame(Agent::ETAT_REMISE, $imprimee->fresh()->etat_carte);
    }

    public function test_annuler_la_remise_et_repasser_en_non_imprimee(): void
    {
        $agent = $this->agent('111111A', ['carte_imprimee_le' => now(), 'carte_remise_le' => now()]);
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.agents.etat-carte'), ['action' => 'annuler_remise', 'agents' => [$agent->id]]);
        $this->assertSame(Agent::ETAT_A_REMETTRE, $agent->fresh()->etat_carte);

        $this->actingAs($admin)->post(route('admin.agents.etat-carte'), ['action' => 'non_imprimee', 'agents' => [$agent->id]]);
        $this->assertSame(Agent::ETAT_NON_IMPRIMEE, $agent->fresh()->etat_carte);
    }

    public function test_onglets_filtrent_la_liste(): void
    {
        $this->agent('111111A', ['nom' => 'NONIMPRIME']);
        $this->agent('222222B', ['nom' => 'AREMETTRE', 'carte_imprimee_le' => now()]);
        $this->agent('333333C', ['nom' => 'REMIS', 'carte_imprimee_le' => now(), 'carte_remise_le' => now()]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.agents.index', ['etat' => Agent::ETAT_A_REMETTRE]))
            ->assertOk()
            ->assertSee('AREMETTRE')
            ->assertDontSee('NONIMPRIME')
            ->assertDontSee('REMIS ');
    }

    public function test_page_carte_affiche_l_etat(): void
    {
        $agent = $this->agent('111111A', ['carte_imprimee_le' => now()]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.agents.badge', $agent))
            ->assertOk()
            ->assertSee('à remettre');
    }
}
