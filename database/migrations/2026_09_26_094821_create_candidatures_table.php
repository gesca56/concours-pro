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
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('concours_id')->constrained('concours')->cascadeOnDelete();
            $table->string('numero_anonymat')->nullable()->unique();
            $table->enum('statut', [
                'en_attente', 'eligible', 'inelegible', 'validee', 'rejetee', 'admise', 'recalee',
            ])->default('en_attente');
            $table->decimal('note_totale', 5, 2)->nullable();
            $table->timestamp('date_soumission')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'concours_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
