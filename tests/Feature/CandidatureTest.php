<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutConcours;
use App\Models\Concours;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidatureTest extends TestCase
{
    use RefreshDatabase;

    private function concoursOuvert(array $overrides = []): Concours
    {
        return Concours::create(array_merge([
            'nom' => 'Concours CAP/PL',
            'code' => 'TEST-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'age_min' => 17,
            'age_max' => 25,
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ], $overrides));
    }

    public function test_un_candidat_eligible_peut_soumettre_une_candidature(): void
    {
        $concours = $this->concoursOuvert();
        $candidat = User::factory()->create(['role' => Role::Candidat, 'date_naissance' => now()->subYears(20)]);

        $response = $this->actingAs($candidat)->post(route('candidatures.store'), [
            'concours_id' => $concours->id,
            'diplome_candidat' => 'BEPC',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('candidatures', [
            'user_id' => $candidat->id,
            'concours_id' => $concours->id,
            'statut' => 'en_attente',
        ]);
    }

    public function test_un_diplome_non_conforme_est_rejete(): void
    {
        $concours = $this->concoursOuvert();
        $candidat = User::factory()->create(['role' => Role::Candidat, 'date_naissance' => now()->subYears(20)]);

        $response = $this->actingAs($candidat)->post(route('candidatures.store'), [
            'concours_id' => $concours->id,
            'diplome_candidat' => 'BAC',
        ]);

        $response->assertSessionHasErrors('diplome_candidat');
        $this->assertDatabaseMissing('candidatures', ['user_id' => $candidat->id]);
    }

    public function test_on_ne_peut_pas_sinscrire_a_un_concours_non_ouvert(): void
    {
        $concours = $this->concoursOuvert(['statut' => StatutConcours::Cloture]);
        $candidat = User::factory()->create(['role' => Role::Candidat, 'date_naissance' => now()->subYears(20)]);

        $response = $this->actingAs($candidat)->post(route('candidatures.store'), [
            'concours_id' => $concours->id,
            'diplome_candidat' => 'BEPC',
        ]);

        $response->assertSessionHasErrors('concours_id');
    }

    public function test_un_candidat_ne_peut_pas_voir_la_candidature_dun_autre(): void
    {
        $concours = $this->concoursOuvert();
        $proprietaire = User::factory()->create(['role' => Role::Candidat]);
        $intrus = User::factory()->create(['role' => Role::Candidat]);

        $candidature = $proprietaire->candidatures()->create([
            'concours_id' => $concours->id,
            'statut' => 'en_attente',
        ]);

        $response = $this->actingAs($intrus)->get(route('candidatures.show', $candidature));

        $response->assertForbidden();
    }
}
