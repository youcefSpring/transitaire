<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chauffeurs', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('nom')->comment('Nom du chauffeur (§11)');
            $table->string('telephone', 20)->comment('Téléphone du chauffeur (§11)');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chauffeurs');
    }
};
