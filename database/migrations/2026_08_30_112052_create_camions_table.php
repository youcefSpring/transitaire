<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camions', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('immatriculation', 50)->unique()->comment('Immatriculation du camion (§11) [PROPOSÉ]');
            $table->text('notes')->nullable()->comment('Notes');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camions');
    }
};
