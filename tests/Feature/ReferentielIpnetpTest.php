<?php

namespace Tests\Feature;

use App\Enums\StatutConcours;
use App\Models\Concours;
use App\Models\User;
use App\Rules\EligibiliteConcours;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReferentielIpnetpTest extends TestCase
{
    use RefreshDatabase;

    private function concours(array $attributs = []): Concours
    {
        return Concours::create($attributs + [
            'nom' => 'Concours direct CAP/PC — Électrotechnique',
            'code' => 'TEST-'.uniqid(),
            'cycle' => 'CAP/PC',
            'filiere' => 'Industriel',
            'diplome_requis' => 'Licence professionnelle, BTS, DUT',
            'age_min' => 18,
            'age_max' => 39,
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ]);
    }

    /** @return list<string> */
    private function erreurs(Concours $concours, User $candidat, string $diplome): array
    {
        $messages = [];
        (new EligibiliteConcours($concours, $candidat))->validate('diplome_candidat', $diplome, function ($m) use (&$messages) {
            $messages[] = $m;
        });

        return $messages;
    }

    public function test_les_pages_publiques_sont_accessibles(): void
    {
        $this->concours();

        foreach (['/', '/l-institut', '/les-concours', '/guide-du-candidat'] as $url) {
            $this->get($url)->assertOk()->assertSee('IPNETP');
        }

        $this->get('/les-concours')->assertSee('CAP/IAFPB')->assertSee('Électrotechnique');
        $this->get('/guide-du-candidat')->assertSee('non-bégaiement');
    }

    public function test_plusieurs_diplomes_sont_admis(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['date_naissance' => now()->subYears(25)]);

        $this->assertSame(['Licence professionnelle', 'BTS', 'DUT'], $concours->diplomesAdmis());
        $this->assertSame([], $this->erreurs($concours, $candidat, 'bts'));
        $this->assertSame([], $this->erreurs($concours, $candidat, 'DUT'));
        $this->assertNotEmpty($this->erreurs($concours, $candidat, 'Master'));
    }

    public function test_age_apprecie_au_premier_janvier_de_l_annee_du_concours(): void
    {
        $concours = $this->concours(['date_concours' => Carbon::create(2026, 7, 15)]);

        // 40 ans en mars 2026, mais encore 39 ans au 1er janvier 2026 : éligible.
        $limite = User::factory()->create(['date_naissance' => Carbon::create(1986, 3, 10)]);
        $this->assertSame([], $this->erreurs($concours, $limite, 'BTS'));

        // Déjà 40 ans au 1er janvier 2026 : trop âgé.
        $tropAge = User::factory()->create(['date_naissance' => Carbon::create(1985, 12, 20)]);
        $this->assertStringContainsString('1 janvier 2026', $this->erreurs($concours, $tropAge, 'BTS')[0]);
    }

    public function test_page_preparation_connexion_et_erreur_404_en_francais(): void
    {
        $this->get('/preparer-le-concours')->assertOk()->assertSee('Simulez votre moyenne');
        $this->get('/login')->assertOk()->assertSee('Se connecter')->assertDontSee('Remember me');
        $this->get('/register')->assertOk()->assertSee('Nom et prénoms');
        $this->get('/page-inexistante')->assertNotFound()->assertSee('Page introuvable');
    }

    public function test_le_tableau_de_bord_indique_la_prochaine_etape(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['role' => \App\Enums\Role::Candidat, 'date_naissance' => now()->subYears(25)]);
        $candidature = $candidat->candidatures()->create([
            'concours_id' => $concours->id, 'diplome_candidat' => 'BTS', 'statut' => 'en_attente', 'date_soumission' => now(),
        ]);

        $this->actingAs($candidat)->get('/candidat')
            ->assertOk()
            ->assertSee("Réglez les frais d'inscription");

        $candidature->paiements()->create(['type' => 'inscription', 'montant' => 25000, 'mode_paiement' => 'mobile_money', 'reference_transaction' => 'T1', 'statut' => 'valide', 'date_paiement' => now()]);

        $this->assertStringStartsWith('Complétez votre dossier (8 pièces', $candidature->fresh()->prochaineEtape()['titre']);
    }

    public function test_les_nouvelles_pieces_du_dossier_sont_acceptees(): void
    {
        $this->assertArrayHasKey('casier_judiciaire', config('ipnetp.types_documents'));
        $this->assertArrayHasKey('certificat_nationalite', config('ipnetp.types_documents'));
    }
}
