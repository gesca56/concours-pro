<?php

namespace Tests\Unit;

use App\Enums\StatutConcours;
use App\Models\Concours;
use App\Models\User;
use App\Rules\EligibiliteConcours;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EligibiliteConcoursTest extends TestCase
{
    use RefreshDatabase;

    private function concours(): Concours
    {
        return Concours::create([
            'nom' => 'Concours Test',
            'code' => 'TEST-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'age_min' => 17,
            'age_max' => 25,
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ]);
    }

    public function test_candidat_eligible_passe_la_validation(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['date_naissance' => now()->subYears(20)]);

        $failed = false;
        (new EligibiliteConcours($concours, $candidat))->validate('diplome_candidat', 'BEPC', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_candidat_trop_jeune_est_rejete(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['date_naissance' => now()->subYears(15)]);

        $messages = [];
        (new EligibiliteConcours($concours, $candidat))->validate('diplome_candidat', 'BEPC', function ($message) use (&$messages) {
            $messages[] = $message;
        });

        $this->assertNotEmpty($messages);
        $this->assertStringContainsString('minimum', $messages[0]);
    }

    public function test_candidat_trop_age_est_rejete(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['date_naissance' => now()->subYears(40)]);

        $messages = [];
        (new EligibiliteConcours($concours, $candidat))->validate('diplome_candidat', 'BEPC', function ($message) use (&$messages) {
            $messages[] = $message;
        });

        $this->assertNotEmpty($messages);
        $this->assertStringContainsString('maximum', $messages[0]);
    }

    public function test_diplome_non_conforme_est_rejete(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['date_naissance' => now()->subYears(20)]);

        $messages = [];
        (new EligibiliteConcours($concours, $candidat))->validate('diplome_candidat', 'BAC', function ($message) use (&$messages) {
            $messages[] = $message;
        });

        $this->assertNotEmpty($messages);
        $this->assertStringContainsString('diplôme requis', $messages[0]);
    }

    public function test_diplome_est_compare_sans_tenir_compte_de_la_casse(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['date_naissance' => now()->subYears(20)]);

        $failed = false;
        (new EligibiliteConcours($concours, $candidat))->validate('diplome_candidat', '  bepc  ', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_date_naissance_manquante_est_rejetee(): void
    {
        $concours = $this->concours();
        $candidat = User::factory()->create(['date_naissance' => null]);

        $messages = [];
        (new EligibiliteConcours($concours, $candidat))->validate('diplome_candidat', 'BEPC', function ($message) use (&$messages) {
            $messages[] = $message;
        });

        $this->assertNotEmpty($messages);
    }
}
