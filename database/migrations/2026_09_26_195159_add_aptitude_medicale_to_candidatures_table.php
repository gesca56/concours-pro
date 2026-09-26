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
        Schema::table('candidatures', function (Blueprint $table) {
            $table->timestamp('visite_medicale_programmee_le')->nullable()->after('numero_anonymat');
            $table->enum('aptitude_medicale', ['en_attente', 'apte', 'inapte'])
                ->default('en_attente')
                ->after('visite_medicale_programmee_le');
            $table->text('motif_inaptitude')->nullable()->after('aptitude_medicale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn(['visite_medicale_programmee_le', 'aptitude_medicale', 'motif_inaptitude']);
        });
    }
};
