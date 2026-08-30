<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conteneurs', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('numero', 20)->unique()->comment('Numéro de conteneur (§4)');
            $table->string('type', 50)->comment('Type de conteneur (§4)');
            $table->string('numero_bl')->comment('Numéro BL (§4)');
            $table->string('navire')->nullable()->comment('Navire (§4)');
            $table->string('port_depart')->comment('Port de départ (§4)');
            $table->string('port_arrivee')->comment("Port d'arrivée (§4)");
            $table->date('date_eta')->comment('Date ETA (§4)');
            $table->date('date_ata')->nullable()->comment('Date ATA (§4)');
            $table->foreignId('client_id')->comment('Client (§4)')->constrained();
            $table->foreignId('dossier_id')->comment('Dossier associé (§4)')->constrained();
            $table->string('statut', 20)->default('en_attente')->comment('Statut — enum ConteneurStatut : en attente, sorti, livré, retourné (§4)');
            $table->date('date_sortie')->nullable()->comment('Date de sortie (§4)');
            $table->date('date_retour')->nullable()->comment('Date de retour (§4)');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index('statut');
            $table->index('date_eta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conteneurs');
    }
};
