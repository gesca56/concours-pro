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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['candidat', 'receptionniste', 'medecin', 'enseignant', 'administration'])
                ->default('candidat')
                ->after('email');
            $table->string('telephone')->nullable()->after('role');
            $table->date('date_naissance')->nullable()->after('telephone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'telephone', 'date_naissance']);
        });
    }
};
