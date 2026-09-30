<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidature extends Model
{
    protected $fillable = [
        'user_id',
        'concours_id',
        'diplome_candidat',
        'numero_anonymat',
        'jeton_convocation',
        'visite_medicale_programmee_le',
        'aptitude_medicale',
        'motif_inaptitude',
        'statut',
        'note_totale',
        'date_soumission',
    ];

    protected function casts(): array
    {
        return [
            'note_totale' => 'decimal:2',
            'date_soumission' => 'datetime',
            'visite_medicale_programmee_le' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function candidat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Concours, $this>
     */
    public function concours(): BelongsTo
    {
        return $this->belongsTo(Concours::class);
    }

    /**
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * @return HasMany<Paiement, $this>
     */
    /**
     * Pièces prêtes pour la visite médicale : aucune en attente et au moins une validée.
     * Les pièces rejetées sont ignorées (le candidat les remplace par un nouveau dépôt).
     */
    public function piecesVerifiees(): bool
    {
        return $this->documents->contains('statut_verification', 'valide')
            && ! $this->documents->contains('statut_verification', 'en_attente');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function paiementValide(string $type): bool
    {
        return $this->paiements->contains(fn ($p) => $p->type === $type && $p->statut === 'valide');
    }

    /**
     * Pièces du dossier IPNETP attendues en ligne et pas encore déposées
     * (hors pièces rejetées, qui doivent être redéposées).
     * L'attestation d'expérience n'est exigée que pour certains concours.
     *
     * @return list<string> libellés des pièces manquantes
     */
    public function piecesManquantes(): array
    {
        $deposees = $this->documents->where('statut_verification', '!=', 'rejete')->pluck('type')->all();

        return collect(config('ipnetp.pieces'))
            ->filter(fn ($p) => $p['type'] && $p['type'] !== 'attestation_experience' && ! in_array($p['type'], $deposees, true))
            ->pluck('libelle')
            ->values()
            ->all();
    }

    /**
     * Les six étapes du parcours IPNETP et leur état pour ce dossier.
     *
     * @return list<array{libelle: string, faite: bool}>
     */
    public function etapesParcours(): array
    {
        $delibere = in_array($this->statut, ['admise', 'recalee'], true) && $this->note_totale !== null;

        return [
            ['libelle' => 'Préinscription', 'faite' => true],
            ['libelle' => 'Paiements', 'faite' => $this->paiementValide('inscription') && $this->paiementValide('visite_medicale')],
            ['libelle' => 'Pièces vérifiées', 'faite' => $this->visite_medicale_programmee_le !== null],
            ['libelle' => 'Visite médicale', 'faite' => $this->aptitude_medicale === 'apte'],
            ['libelle' => 'Épreuves', 'faite' => $this->note_totale !== null],
            ['libelle' => 'Résultats', 'faite' => $delibere],
        ];
    }

    /**
     * Ce que le candidat doit faire (ou attendre) maintenant.
     *
     * @return array{ton: string, titre: string, texte: string, action?: string, lien?: string}
     */
    public function prochaineEtape(): array
    {
        $dossier = route('candidatures.show', $this);

        return match (true) {
            $this->statut === 'admise' => ['ton' => 'succes', 'titre' => 'Félicitations, vous êtes admis(e) !', 'texte' => "Vous intégrez l'IPNETP comme élève-professeur. Surveillez les communiqués pour la date de rentrée et les formalités d'inscription."],
            $this->statut === 'recalee' && $this->aptitude_medicale === 'inapte' => ['ton' => 'echec', 'titre' => 'Candidature non retenue (visite médicale)', 'texte' => $this->motif_inaptitude ?: "Le service médical vous a déclaré inapte. Rapprochez-vous du secrétariat des concours pour toute contestation."],
            in_array($this->statut, ['recalee', 'rejetee', 'inelegible'], true) => ['ton' => 'echec', 'titre' => 'Candidature non retenue', 'texte' => "Votre candidature n'a pas été retenue pour cette session. Vous pourrez vous présenter à la prochaine session si vous remplissez toujours les conditions."],
            ! $this->paiementValide('inscription') => ['ton' => 'action', 'titre' => "Réglez les frais d'inscription", 'texte' => 'Les '.number_format($this->concours->frais_inscription, 0, ',', ' ')." FCFA d'inscription confirment votre préinscription.", 'action' => 'Payer en Mobile Money', 'lien' => $dossier],
            count($manquantes = $this->piecesManquantes()) > 0 => ['ton' => 'action', 'titre' => 'Complétez votre dossier ('.count($manquantes).' pièce'.(count($manquantes) > 1 ? 's' : '').' manquante'.(count($manquantes) > 1 ? 's' : '').')', 'texte' => 'À déposer : '.implode(', ', array_slice($manquantes, 0, 3)).(count($manquantes) > 3 ? '…' : '.'), 'action' => 'Déposer mes pièces', 'lien' => $dossier],
            ! $this->paiementValide('visite_medicale') => ['ton' => 'action', 'titre' => 'Réglez les frais de visite médicale', 'texte' => 'Ce second paiement débloque votre convocation et la programmation de la visite médicale.', 'action' => 'Payer la visite médicale', 'lien' => $dossier],
            $this->visite_medicale_programmee_le === null => ['ton' => 'attente', 'titre' => 'Dossier en cours de vérification', 'texte' => "Le secrétariat contrôle vos pièces une à une. Pensez à déposer aussi votre dossier physique, dans la chemise de couleur de votre concours."],
            $this->aptitude_medicale === 'en_attente' => ['ton' => 'action', 'titre' => 'Présentez-vous à la visite médicale', 'texte' => 'Programmée le '.$this->visite_medicale_programmee_le->translatedFormat('l j F Y à H\hi').'. Munissez-vous de votre pièce d\'identité et de votre convocation.'],
            $this->note_totale === null => ['ton' => 'action', 'titre' => 'Préparez les épreuves écrites', 'texte' => ($this->concours->date_concours ? 'Écrits le '.$this->concours->date_concours->translatedFormat('j F Y').'. ' : '')."Composition française (coef. 3) et épreuve de spécialité (coef. 5). Présentez votre convocation à QR Code à l'entrée.", 'action' => 'Conseils de préparation', 'lien' => route('pages.preparation')],
            default => ['ton' => 'attente', 'titre' => 'Copies corrigées, délibération en cours', 'texte' => 'Le jury délibère. Les résultats apparaîtront ici dès leur publication.'],
        };
    }
}
