<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\TentativeQuiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $user = request()->user();
        $this->autoriser($quiz);

        return view('formation.quiz.show', [
            'quiz' => $quiz->load('questions', 'module'),
            'tentatives' => $quiz->tentatives()->where('user_id', $user->id)->get(),
            'restantes' => $quiz->tentativesRestantes($user),
        ]);
    }

    public function store(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        $this->autoriser($quiz);

        abort_if($quiz->tentativesRestantes($user) === 0, 422, 'Vous avez utilisé toutes vos tentatives pour ce quiz.');

        $request->validate(['reponses' => ['nullable', 'array']]);

        $tentative = $quiz->corriger($user, $request->input('reponses', []));

        return redirect()->route('formation.quiz.resultat', $tentative);
    }

    public function resultat(TentativeQuiz $tentative)
    {
        abort_unless($tentative->user_id === request()->user()->id, 403);

        $quiz = $tentative->quiz->load('questions', 'module');

        return view('formation.quiz.resultat', [
            'tentative' => $tentative,
            'quiz' => $quiz,
            'restantes' => $quiz->tentativesRestantes(request()->user()),
        ]);
    }

    private function autoriser(Quiz $quiz): void
    {
        abort_unless(
            $quiz->publie && $quiz->questions()->exists() && $quiz->module->estAccessiblePar(request()->user()),
            403,
            "Ce quiz n'est pas ouvert à votre compte."
        );
    }
}
