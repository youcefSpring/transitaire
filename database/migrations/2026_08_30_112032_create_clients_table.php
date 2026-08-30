<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('raison_sociale')->comment('Raison sociale / nom du client (§1)');
            $table->string('nif', 20)->comment("NIF — Numéro d'Identification Fiscale (§1)");
            $table->string('nis', 20)->comment("NIS — Numéro d'Identification Statistique (§1)");
            $table->string('rc', 30)->comment('RC — Registre de Commerce (§1)');
            $table->string('adresse')->comment('Adresse (§1)');
            $table->string('telephone', 20)->comment('Téléphone (§1)');
            $table->string('email')->comment('Email (§1)');
            $table->string('conditions_paiement')->comment('Conditions de paiement (§1)');
            $table->foreignId('created_by')->nullable()->comment('Utilisateur créateur')->constrained('users');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->unique('nif');
            $table->unique('nis');
            $table->unique('rc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
