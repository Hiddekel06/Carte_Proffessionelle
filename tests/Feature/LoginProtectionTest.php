<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_connexion_reussie_avec_le_bon_mot_de_passe(): void
    {
        $user = User::factory()->create(['email' => 'admin@test.sn']);

        $this->post('/login', ['email' => 'admin@test.sn', 'password' => 'password'])
            ->assertRedirect(route('admin.agents.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_compte_bloque_apres_cinq_echecs_meme_avec_le_bon_mot_de_passe(): void
    {
        User::factory()->create(['email' => 'admin@test.sn']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'admin@test.sn', 'password' => 'mauvais']);
        }

        $this->post('/login', ['email' => 'admin@test.sn', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertStringContainsString(
            'Trop de tentatives',
            session('errors')->first('email')
        );
    }

    public function test_un_succes_remet_le_compteur_a_zero(): void
    {
        User::factory()->create(['email' => 'admin@test.sn']);

        for ($i = 0; $i < 4; $i++) {
            $this->post('/login', ['email' => 'admin@test.sn', 'password' => 'mauvais']);
        }
        $this->post('/login', ['email' => 'admin@test.sn', 'password' => 'password']);
        $this->assertAuthenticated();
        $this->post('/logout');

        for ($i = 0; $i < 4; $i++) {
            $this->post('/login', ['email' => 'admin@test.sn', 'password' => 'mauvais']);
        }
        $this->post('/login', ['email' => 'admin@test.sn', 'password' => 'password']);
        $this->assertAuthenticated();
    }

    public function test_la_page_detail_agent_redirige_vers_la_modification(): void
    {
        $agent = \App\Models\Agent::create(['matricule' => '123456A', 'prenom' => 'Awa', 'nom' => 'Ndiaye']);

        $this->actingAs(User::factory()->create())
            ->get("/admin/agents/{$agent->id}")
            ->assertRedirect(route('admin.agents.edit', $agent));
    }

    public function test_la_page_detail_agent_inexistant_renvoie_404(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/agents/99999')
            ->assertNotFound();
    }
}
