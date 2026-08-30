<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dedouanement_etapes', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('dossier_id')->comment('Dossier concerné (§6)')->constrained()->cascadeOnDelete();
            $table->string('etape', 30)->comment('Étape — enum DouaneEtape : déclaration, dépôt, contrôle documentaire, visite éventuelle, liquidation, paiement, mainlevée, sortie (§6)');
            $table->foreignId('executed_by')->comment("Personne ayant effectué l'étape (§6/§15)")->constrained('users');
            $table->dateTime('executed_at')->comment("Date et heure d'exécution (§6)");
            $table->text('notes')->nullable()->comment('Notes');
            $table->timestamp('created_at')->nullable()->comment('Créé le');
            $table->index(['dossier_id', 'etape']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dedouanement_etapes');
    }
};
