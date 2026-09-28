<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutConcours;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Régressions corrigées lors de l'audit du parcours complet.
 */
class CorrectifsParcoursTest extends TestCase
{
    use RefreshDatabase;

    private function concours(): Concours
    {
        return Concours::create([
            'nom' => 'Concours Audit',
            'code' => 'AUD-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'age_min' => 17,
            'age_max' => 30,
            'frais_inscription' => 5000,
            'frais_visite_medicale' => 2000,
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ]);
    }

    private function candidat(): User
    {
        return User::factory()->create(['role' => Role::Candidat, 'date_naissance' => now()->subYears(20)]);
    }

    private function candidature(array $attributs = []): Candidature
    {
        return $this->concours()->candidatures()->create(array_merge([
            'user_id' => $this->candidat()->id,
            'statut' => 'eligible',
        ], $attributs));
    }

    public function test_un_nouvel_inscrit_peut_postuler_grace_a_sa_date_de_naissance(): void
    {
        $concours = $this->concours();

        $this->post('/register', [
            'name' => 'Nouveau Candidat',
            'email' => 'nouveau@example.com',
            'date_naissance' => now()->subYears(19)->toDateString(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->post(route('candidatures.store'), [
            'concours_id' => $concours->id,
            'diplome_candidat' => 'BEPC',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseCount('candidatures', 1);
    }

    public function test_l_inscription_exige_une_date_de_naissance(): void
    {
        $this->post('/register', [
            'name' => 'Sans Date',
            'email' => 'sansdate@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('date_naissance');
    }

    public function test_postuler_deux_fois_au_meme_concours_affiche_une_erreur_et_non_une_500(): void
    {
        $candidat = $this->candidat();
        $concours = $this->concours();
        $donnees = ['concours_id' => $concours->id, 'diplome_candidat' => 'BEPC'];

        $this->actingAs($candidat)->post(route('candidatures.store'), $donnees);
        $this->actingAs($candidat)->post(route('candidatures.store'), $donnees)
            ->assertSessionHasErrors('concours_id');

        $this->assertDatabaseCount('candidatures', 1);
    }

    public function test_un_formulaire_sans_concours_affiche_une_erreur_et_non_une_404(): void
    {
        $this->actingAs($this->candidat())->post(route('candidatures.store'), ['diplome_candidat' => 'BEPC'])
            ->assertSessionHasErrors('concours_id');
    }

    public function test_un_meme_paiement_ne_peut_pas_etre_regle_deux_fois(): void
    {
        $candidature = $this->candidature(['statut' => 'en_attente']);
        $paiement = ['type' => 'inscription', 'numero_telephone' => '0700112233'];

        $this->actingAs($candidature->candidat)->post(route('candidatures.paiements.store', $candidature), $paiement);
        $this->actingAs($candidature->candidat)->post(route('candidatures.paiements.store', $candidature), $paiement)
            ->assertSessionHasErrors('numero_telephone');

        $this->assertSame(1, $candidature->paiements()->where('statut', 'valide')->count());
    }

    public function test_une_piece_rejetee_puis_remplacee_ne_bloque_plus_la_visite_medicale(): void
    {
        $candidature = $this->candidature();
        $candidature->documents()->create(['type' => 'diplome', 'chemin_fichier' => 'a.jpg', 'nom_original' => 'flou.jpg', 'statut_verification' => 'rejete', 'motif_rejet' => 'Illisible']);
        $candidature->documents()->create(['type' => 'diplome', 'chemin_fichier' => 'b.jpg', 'nom_original' => 'net.jpg', 'statut_verification' => 'valide']);
        $receptionniste = User::factory()->create(['role' => Role::Receptionniste]);

        $this->actingAs($receptionniste)->post(route('receptionniste.candidatures.visite-medicale', $candidature))
            ->assertSessionHas('status');

        $this->assertNotNull($candidature->fresh()->visite_medicale_programmee_le);
    }

    public function test_le_medecin_ne_peut_pas_valider_sans_visite_programmee(): void
    {
        $candidature = $this->candidature();
        $medecin = User::factory()->create(['role' => Role::Medecin]);

        $this->actingAs($medecin)->patch(route('medecin.candidatures.valider', $candidature), ['aptitude_medicale' => 'apte'])
            ->assertSessionHas('error');

        $this->assertSame('eligible', $candidature->fresh()->statut);
    }

    public function test_le_candidat_ne_peut_plus_deposer_apres_la_programmation_de_la_visite(): void
    {
        Storage::fake('local');
        $candidature = $this->candidature(['visite_medicale_programmee_le' => now()]);

        $this->actingAs($candidature->candidat)->post(route('candidatures.documents.store', $candidature), [
            'type' => 'autre',
            'fichier' => UploadedFile::fake()->create('ajout.pdf', 10, 'application/pdf'),
        ])->assertSessionHas('error');

        $this->assertSame(0, $candidature->documents()->count());
    }

    public function test_la_receptionniste_peut_ouvrir_une_piece_et_un_autre_candidat_non(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('documents/1/piece.pdf', '%PDF-1.4 test');
        $candidature = $this->candidature();
        $document = $candidature->documents()->create(['type' => 'diplome', 'chemin_fichier' => 'documents/1/piece.pdf', 'nom_original' => 'diplome.pdf', 'statut_verification' => 'en_attente']);

        $this->actingAs(User::factory()->create(['role' => Role::Receptionniste]))
            ->get(route('documents.fichier', $document))->assertOk();
        $this->actingAs($candidature->candidat)
            ->get(route('documents.fichier', $document))->assertOk();
        $this->actingAs($this->candidat())
            ->get(route('documents.fichier', $document))->assertForbidden();
    }

    public function test_une_piece_effacee_du_serveur_affiche_un_message_clair(): void
    {
        Storage::fake('local');
        $candidature = $this->candidature();
        $document = $candidature->documents()->create(['type' => 'diplome', 'chemin_fichier' => 'documents/1/disparu.jpg', 'nom_original' => 'disparu.jpg', 'statut_verification' => 'en_attente']);

        $this->actingAs(User::factory()->create(['role' => Role::Receptionniste]))
            ->get(route('documents.fichier', $document))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_les_ecrans_affichent_le_lien_vers_la_piece_et_le_motif_de_rejet(): void
    {
        $candidature = $this->candidature();
        $document = $candidature->documents()->create(['type' => 'diplome', 'chemin_fichier' => 'a.jpg', 'nom_original' => 'diplome-flou.jpg', 'statut_verification' => 'rejete', 'motif_rejet' => 'Photo illisible']);

        $this->actingAs($candidature->candidat)->get(route('candidatures.show', $candidature))
            ->assertOk()
            ->assertSee(route('documents.fichier', $document))
            ->assertSee('Photo illisible');

        $this->actingAs(User::factory()->create(['role' => Role::Receptionniste]))
            ->get(route('receptionniste.candidatures.show', $candidature))
            ->assertOk()
            ->assertSee(route('documents.fichier', $document));

        $this->actingAs($candidature->candidat)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('date_naissance');
    }

    public function test_l_application_mobile_refuse_les_comptes_non_candidats(): void
    {
        User::factory()->create(['role' => Role::Medecin, 'email' => 'medecin@example.com', 'password' => 'password']);

        $this->postJson('/api/login', ['email' => 'medecin@example.com', 'password' => 'password'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }
}
