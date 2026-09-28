<?php

namespace Tests\Feature\Api;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_candidat_peut_sinscrire_via_lapi(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Nouveau Candidat',
            'email' => 'nouveau@example.com',
            'date_naissance' => '2004-05-12',
            'password' => 'password123',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['user', 'token']);
        $this->assertDatabaseHas('users', ['email' => 'nouveau@example.com', 'role' => 'candidat']);
    }

    public function test_un_utilisateur_peut_se_connecter_et_recevoir_un_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_un_mauvais_mot_de_passe_est_rejete(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'mauvais-mot-de-passe',
        ]);

        $response->assertUnprocessable();
    }

    public function test_un_endpoint_protege_refuse_sans_token(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }

    public function test_un_token_valide_donne_acces_au_profil(): void
    {
        $user = User::factory()->create(['role' => Role::Candidat]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/me', ['Authorization' => "Bearer {$token}"]);

        $response->assertOk();
        $response->assertJsonFragment(['email' => $user->email]);
    }
}
