<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents_commerciaux', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('type', 20)->comment('Type — enum DocumentCommercialType : devis, bon de commande, facture, avoir, reçu, situation client (§8)');
            $table->string('numero', 30)->unique()->comment('Numéro unique du document (§8)');
            $table->foreignId('client_id')->comment('Client concerné (§8)')->constrained();
            $table->foreignId('dossier_id')->nullable()->comment('Dossier lié — nullable : situation client (§8)')->constrained();
            $table->char('devise', 3)->comment('Devise : DZD | EUR | USD (§8)');
            $table->decimal('total_prestations', 14, 2)->default(0)->comment('Total prestations (§8)');
            $table->decimal('total_frais', 14, 2)->default(0)->comment('Total frais (§8)');
            $table->decimal('total_taxes', 14, 2)->default(0)->comment('Total taxes (§8)');
            $table->decimal('remise', 14, 2)->default(0)->comment('Remises (§8)');
            $table->decimal('montant_total', 14, 2)->comment('Montant à payer = prestations + frais + taxes − remises (§8)');
            $table->string('statut', 25)->default('brouillon')->comment('Statut — enum DocumentCommercialStatut [PROPOSÉ G-08]');
            $table->date('date_emission')->nullable()->comment("Date d'émission");
            $table->date('date_echeance')->nullable()->comment('Échéance (§9/§12)');
            $table->foreignId('created_by')->comment('Utilisateur créateur')->constrained('users');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index(['client_id', 'type']);
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_commerciaux');
    }
};
