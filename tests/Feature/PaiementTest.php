<?php

namespace Tests\Feature;

use App\Enums\StatutConcours;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\User;
use App\Services\PaiementEchoueException;
use App\Services\PaiementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementTest extends TestCase
{
    use RefreshDatabase;

    private function candidature(): Candidature
    {
        $concours = Concours::create([
            'nom' => 'Concours Paiement',
            'code' => 'PAY-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ]);

        $candidat = User::factory()->create();

        return Candidature::create([
            'user_id' => $candidat->id,
            'concours_id' => $concours->id,
            'statut' => 'en_attente',
        ]);
    }

    public function test_un_paiement_reussi_est_enregistre_comme_valide(): void
    {
        $candidature = $this->candidature();

        $paiement = app(PaiementService::class)->payer($candidature, 'inscription', 25000, '0700000001');

        $this->assertSame('valide', $paiement->statut);
        $this->assertNotNull($paiement->date_paiement);
        $this->assertDatabaseHas('paiements', [
            'candidature_id' => $candidature->id,
            'type' => 'inscription',
            'statut' => 'valide',
        ]);
    }

    public function test_un_paiement_echoue_leve_une_exception_et_conserve_une_trace(): void
    {
        $candidature = $this->candidature();

        $this->expectException(PaiementEchoueException::class);

        try {
            app(PaiementService::class)->payer($candidature, 'inscription', 25000, '070000000000');
        } finally {
            $this->assertDatabaseHas('paiements', [
                'candidature_id' => $candidature->id,
                'statut' => 'echoue',
            ]);
        }
    }

    public function test_la_candidature_devient_eligible_une_fois_les_deux_paiements_valides(): void
    {
        $candidature = $this->candidature();
        $service = app(PaiementService::class);

        $service->payer($candidature, 'inscription', 25000, '0700000001');
        $this->assertSame('en_attente', $candidature->fresh()->statut);

        $service->payer($candidature, 'visite_medicale', 10000, '0700000002');
        $this->assertSame('eligible', $candidature->fresh()->statut);
        $this->assertNotNull($candidature->fresh()->jeton_convocation);
    }
}
