<?php

namespace Tests\Feature;

use App\Enums\StatutConcours;
use App\Models\Concours;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcoursLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function concours(array $overrides = []): Concours
    {
        return Concours::create(array_merge([
            'nom' => 'Concours Cycle de vie',
            'code' => 'CYC-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subMonth(),
            'date_cloture' => now()->subDay(),
        ], $overrides));
    }

    public function test_un_concours_expire_est_cloture_automatiquement(): void
    {
        $concours = $this->concours();

        $this->artisan('app:cloture-concours-expires')->assertSuccessful();

        $this->assertSame(StatutConcours::Cloture, $concours->fresh()->statut);
    }

    public function test_un_concours_encore_ouvert_nest_pas_cloture(): void
    {
        $concours = $this->concours(['date_cloture' => now()->addMonth()]);

        $this->artisan('app:cloture-concours-expires')->assertSuccessful();

        $this->assertSame(StatutConcours::Ouvert, $concours->fresh()->statut);
    }

    public function test_lattribution_danonymat_echoue_si_le_concours_nest_pas_cloture(): void
    {
        $concours = $this->concours(['statut' => StatutConcours::Ouvert, 'date_cloture' => now()->addMonth()]);

        $this->artisan("app:attribuer-numeros-anonymat {$concours->id}")->assertFailed();
    }

    public function test_lattribution_danonymat_fonctionne_pour_un_concours_cloture(): void
    {
        $concours = $this->concours(['statut' => StatutConcours::Cloture]);
        $candidat = \App\Models\User::factory()->create();
        $candidature = $candidat->candidatures()->create([
            'concours_id' => $concours->id,
            'statut' => 'validee',
        ]);

        $this->artisan("app:attribuer-numeros-anonymat {$concours->id}")->assertSuccessful();

        $this->assertNotNull($candidature->fresh()->numero_anonymat);
    }
}
