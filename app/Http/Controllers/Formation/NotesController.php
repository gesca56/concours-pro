<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Barryvdh\DomPDF\Facade\Pdf;

class NotesController extends Controller
{
    public function index()
    {
        $promotion = $this->promotion();

        return view('formation.notes', [
            'promotion' => $promotion,
            'releve' => $promotion->releve(request()->user()),
        ]);
    }

    public function bulletin()
    {
        $user = request()->user();
        $promotion = $this->promotion();

        $pdf = Pdf::loadView('pdf.bulletin', [
            'promotion' => $promotion,
            'eleve' => $user,
            'matricule' => $user->promotions()->whereKey($promotion->id)->first()->pivot->matricule,
            'releve' => $promotion->releve($user),
        ]);

        return $pdf->stream('bulletin-'.str($user->name)->slug().'.pdf');
    }

    private function promotion(): Promotion
    {
        $promotion = request()->user()->promotionActive();

        abort_unless($promotion, 404, "Le relevé de notes est réservé aux élèves-professeurs inscrits dans une promotion.");

        return $promotion;
    }
}
