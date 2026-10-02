<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module « Gestion pédagogique et e-learning ».
 *
 * Deux publics :
 *  - les candidats, qui suivent des modules de préparation au concours ;
 *  - les admis, devenus élèves-professeurs, inscrits dans une promotion
 *    où ils suivent leurs modules de formation (cours, quiz, devoirs, notes).
 *
 * Toutes les tables ont une clé primaire (exigée par Aiven MySQL).
 * Les supports de cours sont du texte et des liens : aucun fichier n'est
 * stocké sur le disque de Render, qui est effacé à chaque redémarrage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->string('cycle');
            $table->string('specialite')->nullable();
            $table->string('annee_academique');
            $table->foreignId('concours_id')->nullable()->constrained('concours')->nullOnDelete();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('statut')->default('en_cours');
            $table->timestamps();
        });

        Schema::create('inscriptions_promotion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidature_id')->nullable()->constrained()->nullOnDelete();
            $table->string('matricule')->unique();
            $table->timestamps();

            $table->unique(['promotion_id', 'user_id']);
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->text('objectifs')->nullable();
            $table->string('public');
            $table->foreignId('promotion_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('cycle')->nullable();
            $table->foreignId('enseignant_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('coefficient')->default(1);
            $table->unsignedSmallInteger('volume_horaire')->nullable();
            $table->boolean('publie')->default(false);
            $table->timestamps();
        });

        Schema::create('lecons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->string('resume')->nullable();
            $table->longText('contenu');
            $table->string('video_url')->nullable();
            $table->string('lien_ressource')->nullable();
            $table->string('libelle_ressource')->nullable();
            $table->unsignedSmallInteger('duree_minutes')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->boolean('publiee')->default(true);
            $table->timestamps();
        });

        Schema::create('lecons_terminees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['lecon_id', 'user_id']);
        });

        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('consignes')->nullable();
            $table->unsignedSmallInteger('duree_minutes')->nullable();
            $table->unsignedTinyInteger('tentatives_max')->nullable();
            $table->boolean('publie')->default(false);
            $table->timestamps();
        });

        Schema::create('questions_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quiz')->cascadeOnDelete();
            $table->text('enonce');
            $table->json('choix');
            $table->unsignedTinyInteger('bonne_reponse');
            $table->text('explication')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('tentatives_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quiz')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('reponses');
            $table->unsignedSmallInteger('bonnes_reponses');
            $table->unsignedSmallInteger('nombre_questions');
            $table->decimal('note', 5, 2);
            $table->timestamps();
        });

        Schema::create('devoirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('consignes');
            $table->dateTime('date_limite');
            $table->boolean('publie')->default(true);
            $table->timestamps();
        });

        Schema::create('rendus_devoir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devoir_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('contenu');
            $table->string('lien')->nullable();
            $table->dateTime('rendu_le');
            $table->boolean('en_retard')->default(false);
            $table->decimal('note', 5, 2)->nullable();
            $table->text('appreciation')->nullable();
            $table->dateTime('corrige_le')->nullable();
            $table->timestamps();

            $table->unique(['devoir_id', 'user_id']);
        });

        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('debut');
            $table->dateTime('fin');
            $table->string('type')->default('cours');
            $table->string('salle')->nullable();
            $table->string('lien_visio')->nullable();
            $table->string('observations')->nullable();
            $table->timestamps();
        });

        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('titre');
            $table->text('contenu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonces');
        Schema::dropIfExists('seances');
        Schema::dropIfExists('rendus_devoir');
        Schema::dropIfExists('devoirs');
        Schema::dropIfExists('tentatives_quiz');
        Schema::dropIfExists('questions_quiz');
        Schema::dropIfExists('quiz');
        Schema::dropIfExists('lecons_terminees');
        Schema::dropIfExists('lecons');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('inscriptions_promotion');
        Schema::dropIfExists('promotions');
    }
};
