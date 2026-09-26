<?php

namespace Tests\Feature;

use App\Enums\StatutConcours;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignalementPaiementTest extends TestCase
{
    use RefreshDatabase;

    private function paiementValide(): Paiement
    {
        $concours = Concours::create([
            'nom' => 'Concours Signalement',
            'code' => 'SIG-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ]);

        $candidat = User::factory()->create();

        $candidature = Candidature::create([
            'user_id' => $candidat->id,
            'concours_id' => $concours->id,
            'statut' => 'en_attente',
        ]);

        return Paiement::create([
            'candidature_id' => $candidature->id,
            'type' => 'inscription',
            'montant' => 25000,
            'mode_paiement' => 'mobile_money',
            'reference_transaction' => 'MM-'.uniqid(),
            'statut' => 'valide',
            'date_paiement' => now(),
        ]);
    }

    public function test_un_candidat_peut_signaler_un_probleme_sur_son_paiement(): void
    {
        $paiement = $this->paiementValide();
        $candidat = $paiement->candidature->candidat;

        $response = $this->actingAs($candidat)->post(route('paiements.signalement', $paiement), [
            'message' => 'Je ne reconnais pas ce paiement, merci de vérifier.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('signalement_paiements', [
            'paiement_id' => $paiement->id,
            'user_id' => $candidat->id,
            'statut' => 'en_attente',
        ]);
    }

    public function test_un_candidat_ne_peut_pas_signaler_le_paiement_dun_autre(): void
    {
        $paiement = $this->paiementValide();
        $intrus = User::factory()->create();

        $response = $this->actingAs($intrus)->post(route('paiements.signalement', $paiement), [
            'message' => 'Ceci ne devrait pas passer.',
        ]);

        $response->assertForbidden();
    }

    public function test_on_ne_peut_pas_signaler_deux_fois_le_meme_paiement_en_attente(): void
    {
        $paiement = $this->paiementValide();
        $candidat = $paiement->candidature->candidat;

        $paiement->signalements()->create([
            'user_id' => $candidat->id,
            'message' => 'Premier signalement.',
        ]);

        $response = $this->actingAs($candidat)->post(route('paiements.signalement', $paiement), [
            'message' => 'Deuxième tentative.',
        ]);

        $response->assertStatus(422);
        $this->assertSame(1, $paiement->signalements()->count());
    }

    public function test_un_message_trop_court_est_rejete(): void
    {
        $paiement = $this->paiementValide();
        $candidat = $paiement->candidature->candidat;

        $response = $this->actingAs($candidat)->post(route('paiements.signalement', $paiement), [
            'message' => 'Court',
        ]);

        $response->assertSessionHasErrors('message');
    }
}
