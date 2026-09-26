<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

/**
 * Compresse dynamiquement les photos de profil et pièces justificatives
 * (~5 Mo vers ~200 Ko) en conservant le ratio d'aspect, avant stockage
 * dans storage/app/private.
 */
class ImageCompressionService
{
    private const LARGEUR_MAX = 1600;

    private const TAILLE_CIBLE_OCTETS = 200 * 1024;

    private readonly ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * @return string  Le contenu binaire compressé, prêt à être écrit sur le disque.
     */
    public function compresser(UploadedFile $fichier): string
    {
        $image = $this->manager->read($fichier->getRealPath());

        if ($image->width() > self::LARGEUR_MAX) {
            $image->scaleDown(width: self::LARGEUR_MAX);
        }

        $qualite = 82;
        $encoded = $image->encode(new AutoEncoder(quality: $qualite));

        while (strlen((string) $encoded) > self::TAILLE_CIBLE_OCTETS && $qualite > 30) {
            $qualite -= 10;
            $encoded = $image->encode(new AutoEncoder(quality: $qualite));
        }

        return (string) $encoded;
    }
}
