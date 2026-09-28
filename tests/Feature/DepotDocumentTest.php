<?php

namespace Tests\Feature;

use App\Enums\StatutConcours;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\User;
use App\Services\ImageCompressionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DepotDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function candidature(): Candidature
    {
        $concours = Concours::create([
            'nom' => 'Concours Documents',
            'code' => 'DOC-'.uniqid(),
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDay(),
            'date_cloture' => now()->addMonth(),
        ]);

        return $concours->candidatures()->create([
            'user_id' => User::factory()->create()->id,
            'statut' => 'en_attente',
        ]);
    }

    public function test_une_photo_jpg_est_compressee_et_enregistree_en_jpeg(): void
    {
        Storage::fake('local');
        $candidature = $this->candidature();

        $response = $this->actingAs($candidature->candidat)->post(route('candidatures.documents.store', $candidature), [
            'type' => 'diplome',
            'fichier' => UploadedFile::fake()->image('IMG_2026.JPG', 3000, 2000),
        ]);

        $response->assertRedirect(route('candidatures.show', $candidature));
        $document = $candidature->documents()->sole();
        $this->assertStringEndsWith('.jpg', $document->chemin_fichier);
        $this->assertSame('image/jpeg', getimagesizefromstring(Storage::disk('local')->get($document->chemin_fichier))['mime']);
    }

    public function test_une_image_png_est_convertie_en_jpeg(): void
    {
        Storage::fake('local');
        $candidature = $this->candidature();

        $this->actingAs($candidature->candidat)->post(route('candidatures.documents.store', $candidature), [
            'type' => 'acte_naissance',
            'fichier' => UploadedFile::fake()->image('acte.png', 800, 600),
        ])->assertRedirect();

        $contenu = Storage::disk('local')->get($candidature->documents()->sole()->chemin_fichier);
        $this->assertSame('image/jpeg', getimagesizefromstring($contenu)['mime']);
    }

    public function test_un_pdf_est_stocke_tel_quel(): void
    {
        Storage::fake('local');
        $candidature = $this->candidature();

        $this->actingAs($candidature->candidat)->post(route('candidatures.documents.store', $candidature), [
            'type' => 'certificat_medical',
            'fichier' => UploadedFile::fake()->create('certificat.pdf', 300, 'application/pdf'),
        ])->assertRedirect();

        $this->assertStringEndsWith('.pdf', $candidature->documents()->sole()->chemin_fichier);
    }

    public function test_le_fichier_original_est_conserve_si_la_compression_echoue(): void
    {
        Storage::fake('local');
        $candidature = $this->candidature();
        $this->mock(ImageCompressionService::class)
            ->shouldReceive('compresser')->andThrow(new RuntimeException('GD sans support JPEG'));

        $this->actingAs($candidature->candidat)->post(route('candidatures.documents.store', $candidature), [
            'type' => 'diplome',
            'fichier' => UploadedFile::fake()->image('diplome.jpeg'),
        ])->assertRedirect(route('candidatures.show', $candidature));

        $document = $candidature->documents()->sole();
        $this->assertStringEndsWith('.jpeg', $document->chemin_fichier);
        Storage::disk('local')->assertExists($document->chemin_fichier);
    }

    public function test_l_api_mobile_accepte_une_photo(): void
    {
        Storage::fake('local');
        $candidature = $this->candidature();

        $this->actingAs($candidature->candidat, 'sanctum')
            ->postJson("/api/candidatures/{$candidature->id}/documents", [
                'type' => 'diplome',
                'fichier' => UploadedFile::fake()->image('photo.jpg', 1200, 900),
            ])
            ->assertCreated()
            ->assertJsonPath('statut_verification', 'en_attente');
    }
}
