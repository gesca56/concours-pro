<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->cascadeOnDelete();
            $table->enum('type', [
                'acte_naissance', 'diplome', 'photo_identite', 'certificat_medical', 'piece_identite', 'autre',
            ]);
            $table->string('chemin_fichier');
            $table->string('nom_original');
            $table->unsignedInteger('taille_octets')->nullable();
            $table->enum('statut_verification', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->text('motif_rejet')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
