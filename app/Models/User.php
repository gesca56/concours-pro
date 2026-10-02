<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'date_naissance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'date_naissance' => 'date',
        ];
    }

    /**
     * @return HasMany<Candidature, $this>
     */
    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class);
    }

    /**
     * Promotions dans lesquelles l'élève-professeur est inscrit.
     *
     * @return BelongsToMany<Promotion, $this>
     */
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'inscriptions_promotion')
            ->withPivot('matricule', 'candidature_id')
            ->withTimestamps();
    }

    /**
     * Promotion en cours de l'élève (la plus récente), s'il en a une.
     */
    public function promotionActive(): ?Promotion
    {
        return $this->promotions()->where('statut', 'en_cours')->latest('promotions.id')->first();
    }

    /**
     * @return BelongsToMany<Lecon, $this>
     */
    public function leconsTerminees(): BelongsToMany
    {
        return $this->belongsToMany(Lecon::class, 'lecons_terminees')->withTimestamps();
    }

    /**
     * Modules dont l'enseignant est responsable.
     *
     * @return HasMany<Module, $this>
     */
    public function modulesEnseignes(): HasMany
    {
        return $this->hasMany(Module::class, 'enseignant_id');
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }
}
