<?php

namespace Tests\Feature\Api;

use App\Enums\Role;
use App\Enums\StatutConcours;
use App\Models\Concours;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCandidatureTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsCandidatApi(): array
    {
        $candidat = User::factory()->create(['role' => Role::Candidat, 'date_naissance' => now()->subYears(20)]);
        $token = $candidat->createToken('test')->plainTextToken;

        return [$candidat, ['Authorization' => "Bearer {$token}"]];
    }

    public function test_un_candidat_peut_lister_les_concours_ouverts_via_lapi(): void
    {
        [, $headers] = $this->actingAsCandidatApi();

        Concours::create([
            'nom' => 'Concours API', 'code' => 'API-'.uniqid(), 'cycle' => 'CAP/PL', 'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC', 'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(), 'date_cloture' => now()->addMonth(),
        ]);
        Concours::create([
            'nom' => 'Concours Brouillon', 'code' => 'API2-'.uniqid(), 'cycle' => 'CAP/PC', 'filiere' => 'Industriel',
            'diplome_requis' => 'CAP', 'statut' => StatutConcours::Brouillon,
            'date_ouverture' => now()->addWeek(), 'date_cloture' => now()->addMonth(),
        ]);

        $response = $this->getJson('/api/concours', $headers);

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['nom' => 'Concours API']);
    }

    public function test_un_candidat_peut_creer_une_candidature_via_lapi(): void
    {
        [, $headers] = $this->actingAsCandidatApi();

        $concours = Concours::create([
            'nom' => 'Concours API', 'code' => 'API-'.uniqid(), 'cycle' => 'CAP/PL', 'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC', 'age_min' => 17, 'age_max' => 25, 'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(), 'date_cloture' => now()->addMonth(),
        ]);

        $response = $this->postJson('/api/candidatures', [
            'concours_id' => $concours->id,
            'diplome_candidat' => 'BEPC',
        ], $headers);

        $response->assertCreated();
        $this->assertDatabaseHas('candidatures', ['concours_id' => $concours->id, 'statut' => 'en_attente']);
    }

    public function test_un_candidat_ne_peut_pas_voir_la_candidature_dun_autre_via_lapi(): void
    {
        [, $headers] = $this->actingAsCandidatApi();

        $autreCandidat = User::factory()->create(['role' => Role::Candidat]);
        $concours = Concours::create([
            'nom' => 'Concours API', 'code' => 'API-'.uniqid(), 'cycle' => 'CAP/PL', 'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC', 'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(), 'date_cloture' => now()->addMonth(),
        ]);
        $candidature = $autreCandidat->candidatures()->create(['concours_id' => $concours->id, 'statut' => 'en_attente']);

        $response = $this->getJson("/api/candidatures/{$candidature->id}", $headers);

        $response->assertForbidden();
    }
}
