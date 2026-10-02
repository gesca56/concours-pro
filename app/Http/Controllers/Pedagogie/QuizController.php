<?php

namespace App\Http\Controllers\Pedagogie;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizController extends Controller
{
    public function create(Module $module)
    {
        $this->autoriser($module);

        return view('pedagogie.quiz.create', compact('module'));
    }

    public function store(Request $request, Module $module)
    {
        $this->autoriser($module);

        [$donnees, $questions] = $this->valider($request);

        $quiz = DB::transaction(function () use ($module, $donnees, $questions) {
            $quiz = $module->quiz()->create($donnees);
            $quiz->questions()->createMany($questions);

            return $quiz;
        });

        return redirect()->route('pedagogie.quiz.show', $quiz)->with('status', 'Quiz enregistré.');
    }

    public function show(Quiz $quiz)
    {
        $this->autoriser($quiz->module);

        $quiz->load(['module', 'questions', 'tentatives.eleve']);

        return view('pedagogie.quiz.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $this->autoriser($quiz->module);

        return view('pedagogie.quiz.edit', ['quiz' => $quiz->load('questions'), 'module' => $quiz->module]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->autoriser($quiz->module);

        [$donnees, $questions] = $this->valider($request);

        DB::transaction(function () use ($quiz, $donnees, $questions) {
            $quiz->update($donnees);
            $quiz->questions()->delete();
            $quiz->questions()->createMany($questions);
        });

        return redirect()->route('pedagogie.quiz.show', $quiz)->with('status', 'Quiz mis à jour.');
    }

    public function destroy(Quiz $quiz)
    {
        $this->autoriser($quiz->module);

        $quiz->delete();

        return redirect()->route('pedagogie.modules.show', $quiz->module_id)->with('status', 'Quiz supprimé.');
    }

    private function autoriser(Module $module): void
    {
        abort_unless($module->estGerablePar(request()->user()), 403, 'Ce module est géré par un autre enseignant.');
    }

    /**
     * @return array{0: array<string, mixed>, 1: list<array<string, mixed>>}
     */
    private function valider(Request $request): array
    {
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'consignes' => ['nullable', 'string', 'max:2000'],
            'duree_minutes' => ['nullable', 'integer', 'between:1,240'],
            'tentatives_max' => ['nullable', 'integer', 'between:1,20'],
            'publie' => ['boolean'],
            'questions' => ['required', 'array', 'min:1', 'max:60'],
            'questions.*.enonce' => ['required', 'string', 'max:1000'],
            'questions.*.choix' => ['required', 'array', 'min:2', 'max:6'],
            'questions.*.choix.*' => ['required', 'string', 'max:500'],
            'questions.*.bonne_reponse' => ['required', 'integer', 'min:0'],
            'questions.*.explication' => ['nullable', 'string', 'max:1000'],
        ], [
            'questions.required' => 'Ajoutez au moins une question.',
            'questions.*.choix.min' => 'Chaque question doit proposer au moins deux réponses.',
            'questions.*.choix.*.required' => 'Une réponse proposée est vide.',
            'questions.*.bonne_reponse.required' => 'Indiquez la bonne réponse de chaque question.',
        ]);

        $questions = [];
        foreach (array_values($donnees['questions']) as $i => $question) {
            $choix = array_values($question['choix']);
            if ($question['bonne_reponse'] >= count($choix)) {
                throw ValidationException::withMessages([
                    "questions.$i.bonne_reponse" => 'La bonne réponse de la question '.($i + 1).' ne fait pas partie des choix.',
                ]);
            }

            $questions[] = [
                'enonce' => $question['enonce'],
                'choix' => $choix,
                'bonne_reponse' => (int) $question['bonne_reponse'],
                'explication' => $question['explication'] ?? null,
                'ordre' => $i + 1,
            ];
        }

        unset($donnees['questions']);
        $donnees['publie'] = $request->boolean('publie');

        return [$donnees, $questions];
    }
}
