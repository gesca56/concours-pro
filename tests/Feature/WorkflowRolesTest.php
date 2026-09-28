<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutConcours;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowRolesTest extends TestCase
{
    use RefreshDatabase;

    private function concours(array $overrides = []): Concours
    {
        return Concours::create(array_merge([
            'nom' => 'Concours Workflow',
            'code' => 'WF-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'seuil_admission' => 10,
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ], $overrides));
    }

    public function test_le_receptionniste_peut_valider_un_document(): void
    {
        $receptionniste = User::factory()->create(['role' => Role::Receptionniste]);
        $candidature = $this->concours()->candidatures()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'eligible',
        ]);
        $document = $candidature->documents()->create([
            'type' => 'diplome',
            'chemin_fichier' => 'test.jpg',
            'nom_original' => 'diplome.jpg',
            'statut_verification' => 'en_attente',
        ]);

        $response = $this->actingAs($receptionniste)->patch(route('receptionniste.documents.update', $document), [
            'statut_verification' => 'valide',
        ]);

        $response->assertRedirect();
        $this->assertSame('valide', $document->fresh()->statut_verification);
    }

    public function test_la_visite_medicale_ne_peut_pas_etre_programmee_sans_documents_valides(): void
    {
        $receptionniste = User::factory()->create(['role' => Role::Receptionniste]);
        $candidature = $this->concours()->candidatures()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'eligible',
        ]);
        $candidature->documents()->create([
            'type' => 'diplome', 'chemin_fichier' => 'test.jpg', 'nom_original' => 'diplome.jpg',
            'statut_verification' => 'en_attente',
        ]);

        $response = $this->actingAs($receptionniste)
            ->post(route('receptionniste.candidatures.visite-medicale', $candidature));

        $response->assertRedirect()->assertSessionHas('error');
        $this->assertNull($candidature->fresh()->visite_medicale_programmee_le);
    }

    public function test_le_medecin_peut_valider_laptitude_et_faire_progresser_la_candidature(): void
    {
        $medecin = User::factory()->create(['role' => Role::Medecin]);
        $candidature = $this->concours()->candidatures()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'eligible',
            'visite_medicale_programmee_le' => now(),
        ]);

        $response = $this->actingAs($medecin)->patch(route('medecin.candidatures.valider', $candidature), [
            'aptitude_medicale' => 'apte',
        ]);

        $response->assertRedirect();
        $candidature->refresh();
        $this->assertSame('apte', $candidature->aptitude_medicale);
        $this->assertSame('validee', $candidature->statut);
    }

    public function test_un_candidat_declare_inapte_est_recale(): void
    {
        $medecin = User::factory()->create(['role' => Role::Medecin]);
        $candidature = $this->concours()->candidatures()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'eligible',
            'visite_medicale_programmee_le' => now(),
        ]);

        $this->actingAs($medecin)->patch(route('medecin.candidatures.valider', $candidature), [
            'aptitude_medicale' => 'inapte',
            'motif_inaptitude' => 'Tension artérielle trop élevée',
        ]);

        $this->assertSame('recalee', $candidature->fresh()->statut);
    }

    public function test_lenseignant_ne_voit_pas_lidentite_du_candidat_et_peut_noter(): void
    {
        $enseignant = User::factory()->create(['role' => Role::Enseignant]);
        $candidature = $this->concours(['statut' => StatutConcours::Cloture])->candidatures()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'validee',
            'numero_anonymat' => 'ANO-TEST1234',
        ]);

        $dashboard = $this->actingAs($enseignant)->get(route('enseignant.dashboard'));
        $dashboard->assertSee('ANO-TEST1234');
        $dashboard->assertDontSee($candidature->candidat->name);

        $response = $this->actingAs($enseignant)->patch(route('enseignant.candidatures.note', $candidature), [
            'note_totale' => 15,
        ]);

        $response->assertRedirect();
        $this->assertEquals(15, $candidature->fresh()->note_totale);
    }

    public function test_on_ne_peut_pas_noter_deux_fois_la_meme_copie(): void
    {
        $enseignant = User::factory()->create(['role' => Role::Enseignant]);
        $candidature = $this->concours(['statut' => StatutConcours::Cloture])->candidatures()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'validee',
            'numero_anonymat' => 'ANO-TEST5678',
            'note_totale' => 12,
        ]);

        $response = $this->actingAs($enseignant)->patch(route('enseignant.candidatures.note', $candidature), [
            'note_totale' => 18,
        ]);

        $response->assertRedirect()->assertSessionHas('error');
        $this->assertEquals(12, $candidature->fresh()->note_totale);
    }

    public function test_ladministration_peut_lancer_la_deliberation_et_departager_admis_recales(): void
    {
        $admin = User::factory()->create(['role' => Role::Administration]);
        $concours = $this->concours(['statut' => StatutConcours::Cloture, 'seuil_admission' => 10]);

        $admis = $concours->candidatures()->create([
            'user_id' => User::factory()->create()->id, 'statut' => 'validee', 'note_totale' => 12,
        ]);
        $recale = $concours->candidatures()->create([
            'user_id' => User::factory()->create()->id, 'statut' => 'validee', 'note_totale' => 8,
        ]);

        $response = $this->actingAs($admin)->post(route('administration.concours.deliberation', $concours));

        $response->assertRedirect();
        $this->assertSame('admise', $admis->fresh()->statut);
        $this->assertSame('recalee', $recale->fresh()->statut);
        $this->assertSame(StatutConcours::Termine, $concours->fresh()->statut);
    }

    public function test_lattribution_danonymat_via_ladministration_ne_fonctionne_que_pour_un_concours_cloture(): void
    {
        $admin = User::factory()->create(['role' => Role::Administration]);
        $concours = $this->concours(['statut' => StatutConcours::Ouvert]);

        $response = $this->actingAs($admin)->post(route('administration.concours.anonymat', $concours));

        $response->assertRedirect()->assertSessionHas('error');
    }
}
