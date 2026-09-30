<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le dossier IPNETP compte plus de pièces que l'enum d'origine (certificat de
 * nationalité, casier judiciaire, attestation d'expérience, engagement
 * manuscrit). La liste autorisée est désormais validée côté application
 * (config/ipnetp.php) plutôt que figée dans le schéma.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('type', 40)->change();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('type', [
                'acte_naissance', 'diplome', 'photo_identite', 'certificat_medical', 'piece_identite', 'autre',
            ])->change();
        });
    }
};
