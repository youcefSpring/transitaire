<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('dossier_id')->comment('Dossier concerné (§11)')->constrained();
            $table->foreignId('camion_id')->nullable()->comment('Camion interne (§11)')->constrained();
            $table->foreignId('transporteur_externe_id')->nullable()->comment('Transporteur externe — fournisseur (§10/§11)')->constrained('fournisseurs');
            $table->foreignId('chauffeur_id')->nullable()->comment('Chauffeur (§11)')->constrained();
            $table->string('lieu_chargement')->comment('Lieu de chargement : port / aéroport (§11)');
            $table->string('entrepot')->nullable()->comment('Étape entrepôt — Port → Entrepôt → Client (§11)');
            $table->string('destination')->comment('Destination / client (§11)');
            $table->dateTime('date_heure_chargement')->comment('Date et heure de chargement (§11)');
            $table->dateTime('date_heure_livraison')->nullable()->comment('Date et heure de livraison (§11)');
            $table->decimal('frais_transport', 14, 2)->comment('Frais de transport (§11)');
            $table->string('bon_livraison', 100)->nullable()->comment('Référence bon de livraison (§11) [PROPOSÉ]');
            $table->string('statut', 15)->default('planifiee')->comment('Statut — enum LivraisonStatut [PROPOSÉ G-24]');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index('statut');
            $table->index('date_heure_chargement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};
