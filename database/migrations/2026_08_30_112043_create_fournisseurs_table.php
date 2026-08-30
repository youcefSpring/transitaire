<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('nom')->comment('Raison sociale (§10)');
            $table->string('type', 30)->comment('Type — enum FournisseurType : transporteur, compagnie maritime, compagnie aérienne, prestataire, manutention, entrepôt, autre (§10)');
            $table->string('adresse')->nullable()->comment('Adresse (§10)');
            $table->string('telephone', 20)->nullable()->comment('Téléphone (§10)');
            $table->string('email')->nullable()->comment('Email (§10)');
            $table->string('contact')->nullable()->comment('Personne de contact [PROPOSÉ]');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
