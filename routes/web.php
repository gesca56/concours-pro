<?php

use App\Enums\Role;
use App\Http\Controllers\Administration\AnonymatController;
use App\Http\Controllers\Administration\ConcoursController as AdminConcoursController;
use App\Http\Controllers\Administration\DashboardController as AdminDashboardController;
use App\Http\Controllers\Administration\DeliberationController;
use App\Http\Controllers\Administration\PromotionController;
use App\Http\Controllers\Administration\SeanceController;
use App\Http\Controllers\Administration\SignalementController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\ConvocationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentFichierController;
use App\Http\Controllers\Enseignant\DashboardController as EnseignantDashboardController;
use App\Http\Controllers\Enseignant\NoteController;
use App\Http\Controllers\Formation;
use App\Http\Controllers\Medecin\DashboardController as MedecinDashboardController;
use App\Http\Controllers\Medecin\ValidationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\Pedagogie;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Receptionniste\CandidatureController as ReceptionnisteCandidatureController;
use App\Http\Controllers\Receptionniste\DashboardController as ReceptionnisteDashboardController;
use App\Http\Controllers\Receptionniste\DocumentController as ReceptionnisteDocumentController;
use App\Http\Controllers\Receptionniste\VisiteMedicaleController;
use App\Http\Controllers\RecuController;
use App\Http\Controllers\SignalementPaiementController;
use App\Http\Controllers\VerificationQrController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'accueil'])->name('accueil');
Route::get('/l-institut', [PageController::class, 'institut'])->name('pages.institut');
Route::get('/les-concours', [PageController::class, 'concours'])->name('pages.concours');
Route::get('/guide-du-candidat', [PageController::class, 'guide'])->name('pages.guide');
Route::get('/resultats', [PageController::class, 'resultats'])->name('pages.resultats');
Route::get('/preparer-le-concours', [PageController::class, 'preparation'])->name('pages.preparation');

Route::get('/dashboard', function () {
    return redirect()->route(match (request()->user()->role) {
        Role::Candidat => 'candidat.dashboard',
        Role::Receptionniste => 'receptionniste.dashboard',
        Role::Medecin => 'medecin.dashboard',
        Role::Enseignant => 'enseignant.dashboard',
        Role::Administration => 'administration.dashboard',
    });
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Candidat ---
Route::middleware(['auth', 'verified', 'role:candidat'])->prefix('candidat')->name('candidat.')->group(function () {
    Route::get('/', function () {
        $candidatures = request()->user()->candidatures()
            ->with('concours', 'documents', 'paiements')
            ->latest()
            ->get();
        $idsDejaPostules = $candidatures->pluck('concours_id');
        $concoursOuverts = \App\Models\Concours::where('statut', \App\Enums\StatutConcours::Ouvert)
            ->whereNotIn('id', $idsDejaPostules)
            ->orderBy('date_cloture')
            ->get();

        return view('candidat.dashboard', compact('candidatures', 'concoursOuverts'));
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:candidat'])->group(function () {
    Route::resource('candidatures', CandidatureController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('candidatures/{candidature}/fiche', [CandidatureController::class, 'fiche'])->name('candidatures.fiche');
    Route::post('candidatures/{candidature}/paiements', [PaiementController::class, 'store'])->name('candidatures.paiements.store');
    Route::post('candidatures/{candidature}/documents', [DocumentController::class, 'store'])->name('candidatures.documents.store');
    Route::get('candidatures/{candidature}/convocation', [ConvocationController::class, 'show'])->name('candidatures.convocation');
    Route::get('paiements/{paiement}/recu', [RecuController::class, 'show'])->name('paiements.recu');
    Route::post('paiements/{paiement}/signalement', [SignalementPaiementController::class, 'store'])->name('paiements.signalement');
});

// --- Réceptionniste ---
Route::middleware(['auth', 'verified', 'role:receptionniste'])->prefix('receptionniste')->name('receptionniste.')->group(function () {
    Route::get('/', ReceptionnisteDashboardController::class)->name('dashboard');
    Route::get('candidatures/{candidature}', [ReceptionnisteCandidatureController::class, 'show'])->name('candidatures.show');
    Route::patch('documents/{document}', ReceptionnisteDocumentController::class)->name('documents.update');
    Route::post('candidatures/{candidature}/visite-medicale', VisiteMedicaleController::class)->name('candidatures.visite-medicale');
});

// --- Médecin ---
Route::middleware(['auth', 'verified', 'role:medecin'])->prefix('medecin')->name('medecin.')->group(function () {
    Route::get('/', MedecinDashboardController::class)->name('dashboard');
    Route::patch('candidatures/{candidature}', ValidationController::class)->name('candidatures.valider');
});

// --- Enseignant ---
Route::middleware(['auth', 'verified', 'role:enseignant'])->prefix('enseignant')->name('enseignant.')->group(function () {
    Route::get('/', EnseignantDashboardController::class)->name('dashboard');
    Route::patch('candidatures/{candidature}/note', NoteController::class)->name('candidatures.note');
});

// --- Administration ---
Route::middleware(['auth', 'verified', 'role:administration'])->prefix('administration')->name('administration.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('concours', AdminConcoursController::class)->parameters(['concours' => 'concours']);
    Route::post('concours/{concours}/statut', [AdminConcoursController::class, 'changerStatut'])->name('concours.statut');
    Route::post('concours/{concours}/anonymat', AnonymatController::class)->name('concours.anonymat');
    Route::post('concours/{concours}/deliberation', DeliberationController::class)->name('concours.deliberation');
    Route::get('signalements', [SignalementController::class, 'index'])->name('signalements.index');
    Route::patch('signalements/{signalement}', [SignalementController::class, 'update'])->name('signalements.update');

    // Promotions d'élèves-professeurs et emploi du temps
    Route::resource('promotions', PromotionController::class);
    Route::post('promotions/{promotion}/inscriptions', [PromotionController::class, 'inscrireAdmis'])->name('promotions.inscrire');
    Route::delete('promotions/{promotion}/eleves/{eleve}', [PromotionController::class, 'retirer'])->name('promotions.retirer');
    Route::post('promotions/{promotion}/annonces', [PromotionController::class, 'annoncer'])->name('promotions.annoncer');
    Route::post('promotions/{promotion}/seances', [SeanceController::class, 'store'])->name('seances.store');
    Route::delete('seances/{seance}', [SeanceController::class, 'destroy'])->name('seances.destroy');
});

// --- Gestion pédagogique (enseignants + administration) ---
Route::middleware(['auth', 'verified', 'role:enseignant,administration'])->prefix('pedagogie')->name('pedagogie.')->group(function () {
    Route::get('/', Pedagogie\TableauDeBordController::class)->name('dashboard');
    Route::resource('modules', Pedagogie\ModuleController::class)->except('index');

    Route::get('modules/{module}/lecons/create', [Pedagogie\LeconController::class, 'create'])->name('lecons.create');
    Route::post('modules/{module}/lecons', [Pedagogie\LeconController::class, 'store'])->name('lecons.store');
    Route::get('lecons/{lecon}/edit', [Pedagogie\LeconController::class, 'edit'])->name('lecons.edit');
    Route::put('lecons/{lecon}', [Pedagogie\LeconController::class, 'update'])->name('lecons.update');
    Route::delete('lecons/{lecon}', [Pedagogie\LeconController::class, 'destroy'])->name('lecons.destroy');

    Route::get('modules/{module}/quiz/create', [Pedagogie\QuizController::class, 'create'])->name('quiz.create');
    Route::post('modules/{module}/quiz', [Pedagogie\QuizController::class, 'store'])->name('quiz.store');
    Route::get('quiz/{quiz}', [Pedagogie\QuizController::class, 'show'])->name('quiz.show');
    Route::get('quiz/{quiz}/edit', [Pedagogie\QuizController::class, 'edit'])->name('quiz.edit');
    Route::put('quiz/{quiz}', [Pedagogie\QuizController::class, 'update'])->name('quiz.update');
    Route::delete('quiz/{quiz}', [Pedagogie\QuizController::class, 'destroy'])->name('quiz.destroy');

    Route::get('modules/{module}/devoirs/create', [Pedagogie\DevoirController::class, 'create'])->name('devoirs.create');
    Route::post('modules/{module}/devoirs', [Pedagogie\DevoirController::class, 'store'])->name('devoirs.store');
    Route::get('devoirs/{devoir}', [Pedagogie\DevoirController::class, 'show'])->name('devoirs.show');
    Route::get('devoirs/{devoir}/edit', [Pedagogie\DevoirController::class, 'edit'])->name('devoirs.edit');
    Route::put('devoirs/{devoir}', [Pedagogie\DevoirController::class, 'update'])->name('devoirs.update');
    Route::delete('devoirs/{devoir}', [Pedagogie\DevoirController::class, 'destroy'])->name('devoirs.destroy');
    Route::patch('rendus/{rendu}', Pedagogie\RenduController::class)->name('rendus.corriger');

    Route::post('modules/{module}/annonces', [Pedagogie\AnnonceController::class, 'store'])->name('annonces.store');
    Route::delete('annonces/{annonce}', [Pedagogie\AnnonceController::class, 'destroy'])->name('annonces.destroy');
});

// --- E-learning (candidats et élèves-professeurs) ---
Route::middleware(['auth', 'verified', 'role:candidat'])->prefix('e-learning')->name('formation.')->group(function () {
    Route::get('/', Formation\EspaceController::class)->name('index');
    Route::get('modules/{module}', [Formation\ModuleController::class, 'show'])->name('modules.show');
    Route::get('lecons/{lecon}', [Formation\LeconController::class, 'show'])->name('lecons.show');
    Route::post('lecons/{lecon}/terminer', [Formation\LeconController::class, 'terminer'])->name('lecons.terminer');
    Route::get('quiz/{quiz}', [Formation\QuizController::class, 'show'])->name('quiz.show');
    Route::post('quiz/{quiz}', [Formation\QuizController::class, 'store'])->name('quiz.store');
    Route::get('tentatives/{tentative}', [Formation\QuizController::class, 'resultat'])->name('quiz.resultat');
    Route::get('devoirs/{devoir}', [Formation\DevoirController::class, 'show'])->name('devoirs.show');
    Route::post('devoirs/{devoir}', [Formation\DevoirController::class, 'store'])->name('devoirs.store');
    Route::get('notes', [Formation\NotesController::class, 'index'])->name('notes');
    Route::get('notes/bulletin', [Formation\NotesController::class, 'bulletin'])->name('notes.bulletin');
    Route::get('emploi-du-temps', Formation\EmploiDuTempsController::class)->name('emploi-du-temps');
});

// --- Consultation d'une pièce justificative (candidat propriétaire, réception, administration) ---
Route::middleware(['auth', 'verified', 'role:candidat,receptionniste,administration'])->group(function () {
    Route::get('documents/{document}/fichier', DocumentFichierController::class)->name('documents.fichier');
});

// --- Vérification QR Code (réceptionniste + administration) ---
Route::middleware(['auth', 'verified', 'role:receptionniste,administration'])->group(function () {
    Route::get('verification-qr', VerificationQrController::class)->name('verification-qr');
});

require __DIR__.'/auth.php';
