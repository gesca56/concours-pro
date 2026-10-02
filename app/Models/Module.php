<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Module d'enseignement. Son « public » détermine qui peut le suivre :
 *  - preparation : tout candidat (préparation au concours) ;
 *  - formation   : les élèves-professeurs de la promotion rattachée.
 */
class Module extends Model
{
    public const PUBLIC_PREPARATION = 'preparation';

    public const PUBLIC_FORMATION = 'formation';

    protected $fillable = [
        'titre',
        'code',
        'description',
        'objectifs',
        'public',
        'promotion_id',
        'cycle',
        'enseignant_id',
        'coefficient',
        'volume_horaire',
        'publie',
    ];

    protected function casts(): array
    {
        return [
            'publie' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Promotion, $this>
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    /**
     * @return HasMany<Lecon, $this>
     */
    public function lecons(): HasMany
    {
        return $this->hasMany(Lecon::class)->orderBy('ordre')->orderBy('id');
    }

    /**
     * @return HasMany<Quiz, $this>
     */
    public function quiz(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * @return HasMany<Devoir, $this>
     */
    public function devoirs(): HasMany
    {
        return $this->hasMany(Devoir::class)->orderBy('date_limite');
    }

    /**
     * @return HasMany<Annonce, $this>
     */
    public function annonces(): HasMany
    {
        return $this->hasMany(Annonce::class)->latest();
    }

    /**
     * @param  Builder<Module>  $query
     */
    public function scopePreparation(Builder $query): void
    {
        $query->where('public', self::PUBLIC_PREPARATION);
    }

    public function estPreparation(): bool
    {
        return $this->public === self::PUBLIC_PREPARATION;
    }

    public function libellePublic(): string
    {
        return $this->estPreparation()
            ? 'Préparation au concours'.($this->cycle ? ' · '.$this->cycle : '')
            : ($this->promotion?->nom ?? 'Formation');
    }

    /**
     * Un candidat peut suivre un module publié de préparation, ou un module
     * de formation de la promotion dans laquelle il est inscrit.
     */
    public function estAccessiblePar(User $user): bool
    {
        if (! $this->publie || $user->role !== Role::Candidat) {
            return false;
        }

        return $this->estPreparation()
            || $user->promotions()->whereKey($this->promotion_id)->exists();
    }

    /**
     * L'administration gère tous les modules ; un enseignant, les siens.
     */
    public function estGerablePar(User $user): bool
    {
        return $user->role === Role::Administration
            || ($user->role === Role::Enseignant && $this->enseignant_id === $user->id);
    }

    /**
     * Part des leçons publiées que l'élève a terminées (0 à 100).
     */
    public function progression(User $user): int
    {
        $ids = $this->lecons->where('publiee', true)->pluck('id');
        if ($ids->isEmpty()) {
            return 0;
        }

        $terminees = $user->leconsTerminees()->whereIn('lecons.id', $ids)->count();

        return (int) round($terminees / $ids->count() * 100);
    }
}
