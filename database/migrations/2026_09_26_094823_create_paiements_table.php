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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->cascadeOnDelete();
            $table->enum('type', ['inscription', 'visite_medicale']);
            $table->decimal('montant', 10, 2);
            $table->enum('mode_paiement', ['mobile_money', 'especes', 'virement']);
            $table->string('reference_transaction')->unique();
            $table->enum('statut', ['en_attente', 'valide', 'echoue'])->default('en_attente');
            $table->timestamp('date_paiement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
