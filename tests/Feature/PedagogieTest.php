<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutConcours;
use App\Models\Concours;
use App\Models\Devoir;
use App\Models\Lecon;
use App\Models\Module;
use App\Models\Promotion;
use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\PedagogieIpnetpSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedagogieTest extends TestCase
{
    use RefreshDatabase;

    private function promotion(): Promotion
    {
        return Promotion::create([
            'nom' => 'Promotion test',
            'code' => 'PT-'.uniqid(),
            'cycle' => 'CAP/PL',
            'annee_academique' => '2026-2027',
            'statut' => 'en_cours',
        ]);
    }

    private function module(array $overrides = []): Module
    {
        return Module::create(array_merge([
            'titre' => 'Module test',
            'public' => Module::PUBLIC_PREPARATION,
            'publie' => true,
            'coefficient' => 1,
        ], $overrides));
    }

    private function quiz(Module $module, ?int $tentativesMax = null): Quiz
    {
        $quiz = $module->quiz()->create(['titre' => 'Quiz', 'publie' => true, 'tentatives_max' => $tentativesMax]);
        $quiz->questions()->create(['enonce' => 'Q1', 'choix' => ['A', 'B'], 'bonne_reponse' => 0, 'ordre' => 1]);
        $quiz->questions()->create(['enonce' => 'Q2', 'choix' => ['A', 'B', 'C'], 'bonne_reponse' => 2, 'ordre' => 2]);

        return $quiz->load('questions');
    }

    public function test_le_jeu_de_demonstration_se_charge_une_seule_fois(): void
    {
        $this->seed(PedagogieIpnetpSeeder::class);
        $modules = Module::count();

        $this->seed(PedagogieIpnetpSeeder::class);

        $this->assertSame(7, $modules);
        $this->assertSame($modules, Module::count());
        $this->assertSame(1, Promotion::count());
        $this->assertTrue(User::where('email', 'eleve@sigec.test')->first()->promotionActive() !== null);
    }

    public function test_toutes_les_pages_du_module_s_affichent(): void
    {
        $this->seed(PedagogieIpnetpSeeder::class);
        $eleve = User::where('email', 'eleve@sigec.test')->first();
        $enseignant = User::where('role', Role::Enseignant)->first();
        $admin = User::where('role', Role::Administration)->first();
        $moduleFormation = Module::where('public', Module::PUBLIC_FORMATION)->has('devoirs')->first();
        $modulePreparation = Module::preparation()->first();
        $quiz = Quiz::first();
        $devoir = Devoir::first();

        foreach ([
            route('formation.index'),
            route('formation.modules.show', $moduleFormation),
            route('formation.modules.show', $modulePreparation),
            route('formation.lecons.show', $modulePreparation->lecons->first()),
            route('formation.quiz.show', $quiz),
            route('formation.devoirs.show', $devoir),
            route('formation.notes'),
            route('formation.emploi-du-temps'),
            route('formation.emploi-du-temps', ['semaine' => 1]),
            route('formation.notes.bulletin'),
        ] as $url) {
            $this->actingAs($eleve)->get($url)->assertOk();
        }

        foreach ([$enseignant, $admin] as $personnel) {
            foreach ([
                route('pedagogie.dashboard'),
                route('pedagogie.modules.create'),
                route('pedagogie.modules.show', $moduleFormation),
                route('pedagogie.modules.edit', $moduleFormation),
                route('pedagogie.lecons.create', $moduleFormation),
                route('pedagogie.lecons.edit', $moduleFormation->lecons->first()),
                route('pedagogie.quiz.create', $modulePreparation),
                route('pedagogie.quiz.show', $quiz),
                route('pedagogie.quiz.edit', $quiz),
                route('pedagogie.devoirs.create', $moduleFormation),
                route('pedagogie.devoirs.show', $devoir),
                route('pedagogie.devoirs.edit', $devoir),
            ] as $url) {
                $this->actingAs($personnel)->get($url)->assertOk();
            }
        }

        $promotion = Promotion::first();
        foreach ([
            route('administration.promotions.index'),
            route('administration.promotions.create'),
            route('administration.promotions.show', $promotion),
            route('administration.promotions.edit', $promotion),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_un_candidat_suit_la_preparation_mais_pas_la_formation_d_une_autre_promotion(): void
    {
        $candidat = User::factory()->create(['role' => Role::Candidat]);
        $preparation = $this->module();
        $formation = $this->module(['public' => Module::PUBLIC_FORMATION, 'promotion_id' => $this->promotion()->id]);
        $brouillon = $this->module(['publie' => false]);

        $this->actingAs($candidat)->get(route('formation.modules.show', $preparation))->assertOk();
        $this->actingAs($candidat)->get(route('formation.modules.show', $formation))->assertForbidden();
        $this->actingAs($candidat)->get(route('formation.modules.show', $brouillon))->assertForbidden();

        $formation->promotion->inscrire($candidat);
        $this->actingAs($candidat)->get(route('formation.modules.show', $formation))->assertOk();
    }

    public function test_les_roles_sont_cloisonnes(): void
    {
        $this->actingAs(User::factory()->create(['role' => Role::Candidat]))->get(route('pedagogie.dashboard'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => Role::Enseignant]))->get(route('administration.promotions.index'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => Role::Enseignant]))->get(route('formation.index'))->assertForbidden();
    }

    public function test_un_enseignant_ne_gere_que_ses_modules(): void
    {
        $enseignant = User::factory()->create(['role' => Role::Enseignant]);
        $collegue = User::factory()->create(['role' => Role::Enseignant]);

        $this->actingAs($enseignant)->post(route('pedagogie.modules.store'), [
            'titre' => 'Mon module',
            'public' => 'preparation',
            'coefficient' => 3,
            'publie' => '1',
        ])->assertRedirect();

        $module = Module::where('titre', 'Mon module')->first();
        $this->assertSame($enseignant->id, $module->enseignant_id);
        $this->assertSame(1, $module->coefficient);

        $this->actingAs($collegue)->get(route('pedagogie.modules.show', $module))->assertForbidden();
        $this->actingAs($collegue)->post(route('pedagogie.lecons.store', $module), ['titre' => 'X', 'contenu' => 'Y'])->assertForbidden();
    }

    public function test_une_lecon_terminee_fait_progresser_le_module(): void
    {
        $candidat = User::factory()->create(['role' => Role::Candidat]);
        $module = $this->module();
        $premiere = $module->lecons()->create(['titre' => 'L1', 'contenu' => '## Titre', 'ordre' => 1]);
        $seconde = $module->lecons()->create(['titre' => 'L2', 'contenu' => 'Texte', 'ordre' => 2]);

        $this->actingAs($candidat)->post(route('formation.lecons.terminer', $premiere))
            ->assertRedirect(route('formation.lecons.show', $seconde));

        $this->assertSame(50, $module->fresh()->progression($candidat));
    }

    public function test_le_contenu_html_saisi_dans_une_lecon_est_neutralise(): void
    {
        $lecon = new Lecon(['contenu' => "## Partie\n\n<script>alert(1)</script>\n\n[lien](javascript:alert(1))"]);

        $html = $lecon->contenuHtml();

        $this->assertStringContainsString('<h2>Partie</h2>', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_la_video_youtube_est_convertie_en_adresse_d_integration(): void
    {
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', (new Lecon(['video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']))->videoEmbed());
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', (new Lecon(['video_url' => 'https://youtu.be/dQw4w9WgXcQ']))->videoEmbed());
        $this->assertNull((new Lecon(['video_url' => 'https://vimeo.com/123']))->videoEmbed());
    }

    public function test_le_quiz_est_corrige_et_les_tentatives_sont_limitees(): void
    {
        $candidat = User::factory()->create(['role' => Role::Candidat]);
        $quiz = $this->quiz($this->module(), tentativesMax: 1);
        [$q1, $q2] = $quiz->questions;

        $this->actingAs($candidat)->post(route('formation.quiz.store', $quiz), [
            'reponses' => [$q1->id => 0, $q2->id => 1],
        ])->assertRedirect();

        $tentative = $quiz->tentatives()->first();
        $this->assertSame(1, $tentative->bonnes_reponses);
        $this->assertEquals(10, $tentative->note);

        $this->actingAs($candidat)->get(route('formation.quiz.resultat', $tentative))->assertOk()->assertSee('10,00');
        $this->actingAs($candidat)->post(route('formation.quiz.store', $quiz), ['reponses' => []])->assertSessionHas('error');
        $this->assertSame(1, $quiz->tentatives()->count());

        $autre = User::factory()->create(['role' => Role::Candidat]);
        $this->actingAs($autre)->get(route('formation.quiz.resultat', $tentative))->assertForbidden();
    }

    public function test_un_quiz_dont_la_bonne_reponse_n_existe_pas_est_refuse(): void
    {
        $enseignant = User::factory()->create(['role' => Role::Enseignant]);
        $module = $this->module(['enseignant_id' => $enseignant->id]);

        $this->actingAs($enseignant)->post(route('pedagogie.quiz.store', $module), [
            'titre' => 'Quiz',
            'questions' => [['enonce' => 'Q', 'choix' => ['A', 'B'], 'bonne_reponse' => 5]],
        ])->assertSessionHasErrors('questions.0.bonne_reponse');

        $this->actingAs($enseignant)->post(route('pedagogie.quiz.store', $module), [
            'titre' => 'Quiz',
            'publie' => '1',
            'questions' => [['enonce' => 'Q', 'choix' => ['A', 'B'], 'bonne_reponse' => 1, 'explication' => 'Parce que.']],
        ])->assertRedirect();

        $this->assertSame(1, $module->quiz()->first()->questions()->first()->bonne_reponse);
    }

    public function test_le_rendu_et_la_correction_d_un_devoir(): void
    {
        $promotion = $this->promotion();
        $enseignant = User::factory()->create(['role' => Role::Enseignant]);
        $eleve = User::factory()->create(['role' => Role::Candidat]);
        $promotion->inscrire($eleve);
        $module = $this->module(['public' => Module::PUBLIC_FORMATION, 'promotion_id' => $promotion->id, 'enseignant_id' => $enseignant->id]);
        $devoir = $module->devoirs()->create(['titre' => 'D1', 'consignes' => 'Faire', 'date_limite' => now()->subHour()]);

        $this->actingAs($eleve)->post(route('formation.devoirs.store', $devoir), [
            'contenu' => 'Ma réponse détaillée au devoir demandé.',
            'lien' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('lien');

        $this->actingAs($eleve)->post(route('formation.devoirs.store', $devoir), [
            'contenu' => 'Ma réponse détaillée au devoir demandé.',
        ])->assertRedirect();

        $rendu = $devoir->rendus()->first();
        $this->assertTrue($rendu->en_retard);

        $this->actingAs(User::factory()->create(['role' => Role::Enseignant]))
            ->patch(route('pedagogie.rendus.corriger', $rendu), ['note' => 12])->assertForbidden();

        $this->actingAs($enseignant)->patch(route('pedagogie.rendus.corriger', $rendu), ['note' => 15.5, 'appreciation' => 'Bien'])->assertRedirect();
        $this->assertEquals(15.5, $rendu->fresh()->note);

        $this->actingAs($eleve)->post(route('formation.devoirs.store', $devoir), ['contenu' => 'Je modifie ma copie après correction.'])->assertSessionHas('error');
        $this->assertSame('Ma réponse détaillée au devoir demandé.', $rendu->fresh()->contenu);
    }

    public function test_le_releve_pondere_les_modules_et_compte_zero_pour_un_devoir_non_rendu(): void
    {
        $promotion = $this->promotion();
        $eleve = User::factory()->create(['role' => Role::Candidat]);
        $promotion->inscrire($eleve);

        $didactique = $this->module(['titre' => 'A', 'public' => Module::PUBLIC_FORMATION, 'promotion_id' => $promotion->id, 'coefficient' => 3]);
        $legislation = $this->module(['titre' => 'B', 'public' => Module::PUBLIC_FORMATION, 'promotion_id' => $promotion->id, 'coefficient' => 1]);

        $d1 = $didactique->devoirs()->create(['titre' => 'D1', 'consignes' => '.', 'date_limite' => now()->subDay()]);
        $d1->rendus()->create(['user_id' => $eleve->id, 'contenu' => '.', 'rendu_le' => now()->subDays(2), 'note' => 16, 'corrige_le' => now()]);
        $didactique->devoirs()->create(['titre' => 'D2', 'consignes' => '.', 'date_limite' => now()->subDay()]); // non rendu : 0
        $didactique->devoirs()->create(['titre' => 'D3', 'consignes' => '.', 'date_limite' => now()->addWeek()]); // pas encore dû : ignoré

        $d4 = $legislation->devoirs()->create(['titre' => 'D4', 'consignes' => '.', 'date_limite' => now()->subDay()]);
        $d4->rendus()->create(['user_id' => $eleve->id, 'contenu' => '.', 'rendu_le' => now()->subDays(2), 'note' => 12, 'corrige_le' => now()]);

        $releve = $promotion->releve($eleve);

        $this->assertEquals(8, $releve['modules'][0]['moyenne']);
        $this->assertEquals(12, $releve['modules'][1]['moyenne']);
        $this->assertEquals(9, $releve['moyenne']); // (8×3 + 12×1) / 4
        $this->assertSame('Insuffisant', $releve['mention']);
    }

    public function test_l_administration_inscrit_les_admis_d_un_concours_avec_un_matricule(): void
    {
        $admin = User::factory()->create(['role' => Role::Administration]);
        $concours = Concours::create([
            'nom' => 'Concours terminé', 'code' => 'CT-1', 'cycle' => 'CAP/PL', 'filiere' => 'Tertiaire',
            'diplome_requis' => 'Master', 'statut' => StatutConcours::Termine,
            'date_ouverture' => now()->subMonths(3), 'date_cloture' => now()->subMonths(2),
        ]);
        foreach (['admise', 'admise', 'recalee'] as $statut) {
            $concours->candidatures()->create(['user_id' => User::factory()->create(['role' => Role::Candidat])->id, 'statut' => $statut]);
        }

        $this->actingAs($admin)->post(route('administration.promotions.store'), [
            'nom' => 'Promo', 'code' => 'PL26-INFO', 'cycle' => 'CAP/PL', 'annee_academique' => '2026-2027',
            'concours_id' => $concours->id, 'inscrire_admis' => '1', 'statut' => 'en_cours',
        ])->assertRedirect();

        $promotion = Promotion::where('code', 'PL26-INFO')->first();
        $this->assertSame(['PL26-INFO-001', 'PL26-INFO-002'], $promotion->eleves()->pluck('matricule')->sort()->values()->all());

        // Une seconde inscription n'ajoute personne.
        $this->actingAs($admin)->post(route('administration.promotions.inscrire', $promotion), ['concours_id' => $concours->id])->assertRedirect();
        $this->assertSame(2, $promotion->eleves()->count());
    }

    public function test_une_seance_peut_etre_repetee_chaque_semaine(): void
    {
        $admin = User::factory()->create(['role' => Role::Administration]);
        $promotion = $this->promotion();

        $this->actingAs($admin)->post(route('administration.seances.store', $promotion), [
            'jour' => '2026-10-05', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'type' => 'cours', 'repetitions' => 3,
        ])->assertRedirect();

        $this->assertSame(
            ['2026-10-05 08:00', '2026-10-12 08:00', '2026-10-19 08:00'],
            $promotion->seances()->get()->map(fn ($s) => $s->debut->format('Y-m-d H:i'))->all()
        );

        $this->actingAs($admin)->post(route('administration.seances.store', $promotion), [
            'jour' => '2026-10-05', 'heure_debut' => '10:00', 'heure_fin' => '09:00', 'type' => 'cours',
        ])->assertSessionHasErrors('heure_fin');
    }
}
