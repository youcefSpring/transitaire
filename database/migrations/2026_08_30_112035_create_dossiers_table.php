<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('numero', 20)->unique()->comment('Numéro unique du dossier, ex. TR-2026-0158 (§2)');
            $table->foreignId('client_id')->comment('Client du dossier (§1/§2)')->constrained();
            $table->string('type', 10)->comment("Type d'opération — enum TypeOperation : import | export (§2)");
            $table->string('mode_transport', 15)->comment('Mode de transport — enum ModeTransport : maritime | aérien | terrestre (§2)');
            $table->string('port_aeroport')->comment('Port ou aéroport (§2)');
            $table->string('fournisseur_destinataire')->comment('Fournisseur / destinataire (§2)');
            $table->date('date_arrivee_prevue')->comment("Date d'arrivée prévue (§2)");
            $table->date('date_arrivee_reelle')->nullable()->comment("Date d'arrivée réelle (§2)");
            $table->string('numero_bl_awb')->comment('Numéro BL / AWB (§2)');
            $table->unsignedInteger('nombre_colis')->comment('Nombre de colis (§2)');
            $table->decimal('poids', 12, 3)->comment('Poids (§2)');
            $table->decimal('volume', 12, 3)->comment('Volume (§2)');
            $table->string('nature_marchandise')->comment('Nature de la marchandise (§2)');
            $table->decimal('valeur_declaree', 14, 2)->comment('Valeur déclarée (§2)');
            $table->char('devise', 3)->comment('Devise : DZD | EUR | USD (§2/§8)');
            $table->string('incoterm', 10)->comment('Incoterm (§2)');
            $table->string('statut', 20)->default('nouveau')->comment('Statut — enum DossierStatut : nouveau, documents reçus, en cours, dédouanement, douane terminée, livraison, clôturé (§2)');
            $table->boolean('bloque')->default(false)->comment('Dossier bloqué (§2)');
            $table->text('raison_blocage')->nullable()->comment('Raison du blocage — obligatoire si bloqué (§2)');
            $table->foreignId('created_by')->nullable()->comment('Utilisateur créateur')->constrained('users');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index('statut');
            $table->index('type');
            $table->index('mode_transport');
            $table->index('bloque');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
