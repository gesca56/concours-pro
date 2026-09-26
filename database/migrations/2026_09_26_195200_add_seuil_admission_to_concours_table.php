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
        Schema::table('concours', function (Blueprint $table) {
            $table->decimal('seuil_admission', 5, 2)->default(10)->after('frais_visite_medicale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('concours', function (Blueprint $table) {
            $table->dropColumn('seuil_admission');
        });
    }
};
