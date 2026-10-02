<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Promotion d'élèves-professeurs : les admis d'un concours qui suivent
 * ensemble leur formation à l'IPNETP pendant une année académique.
 */
class Promotion extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'cycle',
        'specialite',
        'annee_academique',
        'concours_id',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Concours, $this>
     */
    public function concours(): BelongsTo
    {
        return $this->belongsTo(Concours::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function eleves(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'inscriptions_promotion')
            ->withPivot('matricule', 'candidature_id')
            ->withTimestamps()
            ->orderBy('name');
    }

    /**
     * @return HasMany<Module, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    /**
     * @return HasMany<Seance, $this>
     */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class)->orderBy('debut');
    }

    /**
     * @return HasMany<Annonce, $this>
     */
    public function annonces(): HasMany
    {
        return $this->hasMany(Annonce::class)->latest();
    }

    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Inscrit un élève en lui attribuant le matricule suivant de la promotion
     * (ex. « PL26-INFO-007 »). Sans effet s'il est déjà inscrit.
     */
    public function inscrire(User $eleve, ?Candidature $candidature = null): void
    {
        if ($this->eleves()->whereKey($eleve->id)->exists()) {
            return;
        }

        $rang = DB::table('inscriptions_promotion')->where('promotion_id', $this->id)->count() + 1;
        do {
            $matricule = sprintf('%s-%03d', $this->code, $rang++);
        } while (DB::table('inscriptions_promotion')->where('matricule', $matricule)->exists());

        $this->eleves()->attach($eleve->id, [
            'matricule' => $matricule,
            'candidature_id' => $candidature?->id,
        ]);
    }

    /**
     * Inscrit tous les admis d'un concours. Retourne le nombre de nouveaux inscrits.
     */
    public function inscrireAdmis(Concours $concours): int
    {
        $avant = $this->eleves()->count();

        $concours->candidatures()
            ->where('statut', 'admise')
            ->with('candidat')
            ->get()
            ->each(fn (Candidature $c) => $this->inscrire($c->candidat, $c));

        return $this->eleves()->count() - $avant;
    }

    /**
     * Relevé de notes d'un élève : moyenne de chaque module de formation
     * (devoirs corrigés ; un devoir non rendu après la date limite compte 0)
     * puis moyenne générale pondérée par les coefficients.
     *
     * @return array{modules: list<array{module: Module, moyenne: ?float, notes: int}>, moyenne: ?float, mention: ?string}
     */
    public function releve(User $eleve): array
    {
        $modules = $this->modules()
            ->where('publie', true)
            ->with(['devoirs' => fn ($q) => $q->where('publie', true), 'devoirs.rendus' => fn ($q) => $q->where('user_id', $eleve->id)])
            ->orderBy('titre')
            ->get();

        $lignes = [];
        $somme = 0;
        $coefficients = 0;

        foreach ($modules as $module) {
            $notes = [];
            foreach ($module->devoirs as $devoir) {
                $rendu = $devoir->rendus->first();
                if ($rendu?->note !== null) {
                    $notes[] = (float) $rendu->note;
                } elseif (! $rendu && $devoir->date_limite->isPast()) {
                    $notes[] = 0.0;
                }
            }

            $moyenne = $notes ? round(array_sum($notes) / count($notes), 2) : null;
            $lignes[] = ['module' => $module, 'moyenne' => $moyenne, 'notes' => count($notes)];

            if ($moyenne !== null) {
                $somme += $moyenne * $module->coefficient;
                $coefficients += $module->coefficient;
            }
        }

        $moyenne = $coefficients ? round($somme / $coefficients, 2) : null;

        return ['modules' => $lignes, 'moyenne' => $moyenne, 'mention' => self::mention($moyenne)];
    }

    public static function mention(?float $moyenne): ?string
    {
        return match (true) {
            $moyenne === null => null,
            $moyenne >= 16 => 'Très bien',
            $moyenne >= 14 => 'Bien',
            $moyenne >= 12 => 'Assez bien',
            $moyenne >= 10 => 'Passable',
            default => 'Insuffisant',
        };
    }
}
