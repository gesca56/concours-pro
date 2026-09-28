<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidatureController;
use App\Http\Controllers\Api\ConcoursController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\PdfController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// L'application mobile est réservée aux candidats.
Route::middleware(['auth:sanctum', 'role:candidat'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('concours', [ConcoursController::class, 'index']);

    Route::get('candidatures', [CandidatureController::class, 'index']);
    Route::post('candidatures', [CandidatureController::class, 'store']);
    Route::get('candidatures/{candidature}', [CandidatureController::class, 'show']);
    Route::post('candidatures/{candidature}/paiements', [PaiementController::class, 'store']);
    Route::post('candidatures/{candidature}/documents', [DocumentController::class, 'store']);

    Route::get('candidatures/{candidature}/convocation', [PdfController::class, 'convocation']);
    Route::get('candidatures/{candidature}/fiche', [PdfController::class, 'fiche']);
    Route::get('paiements/{paiement}/recu', [PdfController::class, 'recu']);
});
