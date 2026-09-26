<?php

use App\Enums\Role;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

Route::middleware(['auth', 'verified', 'role:candidat'])->prefix('candidat')->name('candidat.')->group(function () {
    Route::get('/', fn () => view('dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:candidat'])->group(function () {
    Route::resource('candidatures', CandidatureController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('candidatures/{candidature}/paiements', [PaiementController::class, 'store'])->name('candidatures.paiements.store');
    Route::post('candidatures/{candidature}/documents', [DocumentController::class, 'store'])->name('candidatures.documents.store');
});

Route::middleware(['auth', 'verified', 'role:receptionniste'])->prefix('receptionniste')->name('receptionniste.')->group(function () {
    Route::get('/', fn () => view('dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:medecin'])->prefix('medecin')->name('medecin.')->group(function () {
    Route::get('/', fn () => view('dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:enseignant'])->prefix('enseignant')->name('enseignant.')->group(function () {
    Route::get('/', fn () => view('dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:administration'])->prefix('administration')->name('administration.')->group(function () {
    Route::get('/', fn () => view('dashboard'))->name('dashboard');
});

require __DIR__.'/auth.php';
