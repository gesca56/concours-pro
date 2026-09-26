<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_candidat_ne_peut_pas_acceder_a_lespace_administration(): void
    {
        $candidat = User::factory()->create(['role' => Role::Candidat]);

        $response = $this->actingAs($candidat)->get('/administration');

        $response->assertForbidden();
    }

    public function test_un_medecin_ne_peut_pas_acceder_a_lespace_enseignant(): void
    {
        $medecin = User::factory()->create(['role' => Role::Medecin]);

        $response = $this->actingAs($medecin)->get('/enseignant');

        $response->assertForbidden();
    }

    public function test_chaque_role_accede_a_son_propre_espace(): void
    {
        foreach (Role::cases() as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get("/{$role->value}");

            $response->assertOk();
        }
    }

    public function test_un_visiteur_non_authentifie_est_redirige_vers_la_connexion(): void
    {
        $response = $this->get('/candidat');

        $response->assertRedirect(route('login'));
    }
}
