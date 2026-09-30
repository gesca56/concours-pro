<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutConcours;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationEtResultatsTest extends TestCase
{
    use RefreshDatabase;

    private function concours(StatutConcours $statut = StatutConcours::Ouvert): Concours
    {
        return Concours::create([
            'nom' => 'Concours direct CAP/PC — Électrotechnique',
            'code' => 'TEST-'.uniqid(),
            'cycle' => 'CAP/PC',
            'filiere' => 'Industriel',
            'diplome_requis' => 'BTS',
            'statut' => $statut,
            'date_ouverture' => now()->subMonth(),
            'date_cloture' => now()->subWeek(),
        ]);
    }

    private function candidature(Concours $concours, string $nom, array $attributs = []): Candidature
    {
        $candidat = User::factory()->create(['role' => Role::Candidat, 'name' => $nom, 'date_naissance' => now()->subYears(25)]);

        return $candidat->candidatures()->create($attributs + [
            'concours_id' => $concours->id, 'diplome_candidat' => 'BTS', 'statut' => 'en_attente', 'date_soumission' => now(),
        ]);
    }

    public function test_l_administration_repond_a_un_paiement_conteste_et_le_candidat_voit_la_reponse(): void
    {
        $candidature = $this->candidature($this->concours(), 'Koffi Aya');
        $paiement = $candidature->paiements()->create([
            'type' => 'inscription', 'montant' => 25000, 'mode_paiement' => 'mobile_money',
            'reference_transaction' => 'TX-1', 'statut' => 'valide', 'date_paiement' => now(),
        ]);
        $signalement = $paiement->signalements()->create([
            'user_id' => $candidature->user_id, 'message' => 'Débité deux fois par mon opérateur.',
        ]);
        $admin = User::factory()->create(['role' => Role::Administration]);

        $this->actingAs($admin)->get('/administration/signalements')->assertOk()->assertSee('Débité deux fois');
        $this->actingAs($admin)->get('/administration')->assertOk()->assertSee('paiement(s) contesté(s)', false);

        $this->actingAs($admin)
            ->patch("/administration/signalements/{$signalement->id}", ['reponse_administration' => 'Un seul encaissement constaté.'])
            ->assertRedirect();

        $this->assertSame('traite', $signalement->fresh()->statut);

        $this->actingAs($candidature->candidat)
            ->get("/candidatures/{$candidature->id}")
            ->assertSee('Un seul encaissement constaté.');
    }

    public function test_un_candidat_ne_peut_pas_traiter_un_signalement(): void
    {
        $candidat = User::factory()->create(['role' => Role::Candidat]);

        $this->actingAs($candidat)->get('/administration/signalements')->assertForbidden();
    }

    public function test_la_page_resultats_publie_les_admis_des_concours_termines_uniquement(): void
    {
        $termine = $this->concours(StatutConcours::Termine);
        $admis = $this->candidature($termine, 'Kouassi Jean Marc', ['statut' => 'admise', 'note_totale' => 14]);
        $this->candidature($termine, 'Traoré Awa', ['statut' => 'recalee', 'note_totale' => 7]);

        $ouvert = $this->concours();
        $this->candidature($ouvert, 'Bamba Issa');

        $this->get('/resultats')
            ->assertOk()
            ->assertSee(str_pad($admis->id, 5, '0', STR_PAD_LEFT))
            ->assertSee('Kouassi J. M.')
            ->assertDontSee('Kouassi Jean Marc')
            ->assertDontSee('Traoré')
            ->assertDontSee('Bamba')
            ->assertSee('50 %');
    }
}
