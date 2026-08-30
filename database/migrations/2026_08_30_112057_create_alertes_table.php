<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('type', 30)->comment('Type — enum AlerteType : arrivée navire, dossier incomplet, document manquant, dossier bloqué, facture impayée, livraison à effectuer, conteneur à retourner, échéance importante, retard dossier (§12)');
            $table->string('message', 500)->comment("Message d'alerte (§12)");
            $table->foreignId('dossier_id')->nullable()->comment('Dossier concerné (§12)')->constrained()->nullOnDelete();
            $table->string('ref_type', 50)->nullable()->comment('Entité concernée hors dossier [PROPOSÉ]');
            $table->unsignedBigInteger('ref_id')->nullable()->comment("Identifiant de l'entité concernée [PROPOSÉ]");
            $table->date('date_echeance')->nullable()->comment('Échéance associée (§12)');
            $table->string('statut', 15)->default('nouvelle')->comment('Statut — enum AlerteStatut [PROPOSÉ G-23]');
            $table->timestamps();
            $table->index('statut');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
