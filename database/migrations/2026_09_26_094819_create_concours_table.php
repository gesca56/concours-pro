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
        Schema::create('concours', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->enum('cycle', ['CAP/PL', 'CAP/PC', 'CAP/IFPB', 'CAP/IAFPB']);
            $table->enum('filiere', ['Tertiaire', 'Industriel', 'Agricole']);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->string('diplome_requis');
            $table->decimal('frais_inscription', 10, 2)->default(25000);
            $table->decimal('frais_visite_medicale', 10, 2)->default(10000);
            $table->date('date_ouverture');
            $table->date('date_cloture');
            $table->date('date_concours')->nullable();
            $table->enum('statut', ['brouillon', 'ouvert', 'cloture', 'deliberation', 'termine'])
                ->default('brouillon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concours');
    }
};
