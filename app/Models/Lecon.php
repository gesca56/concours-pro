<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Lecon extends Model
{
    protected $fillable = [
        'module_id',
        'titre',
        'resume',
        'contenu',
        'video_url',
        'lien_ressource',
        'libelle_ressource',
        'duree_minutes',
        'ordre',
        'publiee',
    ];

    protected function casts(): array
    {
        return [
            'publiee' => 'boolean',
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
     * Contenu rédigé en Markdown, converti en HTML sûr (le HTML saisi est retiré).
     */
    public function contenuHtml(): string
    {
        return Str::markdown($this->contenu, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Adresse d'intégration d'une vidéo YouTube (formats watch, youtu.be, shorts, embed).
     */
    public function videoEmbed(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $motif = '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~';

        return preg_match($motif, $this->video_url, $m)
            ? 'https://www.youtube-nocookie.com/embed/'.$m[1]
            : null;
    }
}
