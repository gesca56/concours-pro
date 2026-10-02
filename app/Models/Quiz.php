<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Quiz à choix multiples, corrigé automatiquement. Il sert à l'entraînement
 * et n'entre pas dans la moyenne du module (seuls les devoirs comptent).
 */
class Quiz extends Model
{
    protected $table = 'quiz';

    protected $fillable = [
        'module_id',
        'titre',
        'consignes',
        'duree_minutes',
        'tentatives_max',
        'publie',
    ];

    protected function casts(): array
    {
        return [
            'publie' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * @return HasMany<QuestionQuiz, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuestionQuiz::class)->orderBy('ordre')->orderBy('id');
    }

    /**
     * @return HasMany<TentativeQuiz, $this>
     */
    public function tentatives(): HasMany
    {
        return $this->hasMany(TentativeQuiz::class)->latest();
    }

    public function tentativesRestantes(User $user): ?int
    {
        if ($this->tentatives_max === null) {
            return null;
        }

        return max(0, $this->tentatives_max - $this->tentatives()->where('user_id', $user->id)->count());
    }

    /**
     * Corrige une copie et enregistre la tentative (note ramenée sur 20).
     *
     * @param  array<int|string, int|string>  $reponses  indice du choix par identifiant de question
     */
    public function corriger(User $user, array $reponses): TentativeQuiz
    {
        $questions = $this->questions;
        $retenues = [];
        $bonnes = 0;

        foreach ($questions as $question) {
            $choix = $reponses[$question->id] ?? null;
            $choix = is_numeric($choix) ? (int) $choix : null;
            $retenues[$question->id] = $choix;
            if ($choix === $question->bonne_reponse) {
                $bonnes++;
            }
        }

        return $this->tentatives()->create([
            'user_id' => $user->id,
            'reponses' => $retenues,
            'bonnes_reponses' => $bonnes,
            'nombre_questions' => $questions->count(),
            'note' => $questions->count() ? round($bonnes / $questions->count() * 20, 2) : 0,
        ]);
    }
}
