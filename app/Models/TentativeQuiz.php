<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TentativeQuiz extends Model
{
    protected $table = 'tentatives_quiz';

    protected $fillable = [
        'quiz_id',
        'user_id',
        'reponses',
        'bonnes_reponses',
        'nombre_questions',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'reponses' => 'array',
            'note' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Quiz, $this>
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
