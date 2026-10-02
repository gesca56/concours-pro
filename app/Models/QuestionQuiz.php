<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionQuiz extends Model
{
    protected $table = 'questions_quiz';

    protected $fillable = [
        'quiz_id',
        'enonce',
        'choix',
        'bonne_reponse',
        'explication',
        'ordre',
    ];

    protected function casts(): array
    {
        return [
            'choix' => 'array',
            'bonne_reponse' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Quiz, $this>
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
