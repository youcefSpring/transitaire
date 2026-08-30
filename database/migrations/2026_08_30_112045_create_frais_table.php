<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frais', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('dossier_id')->comment('Dossier concerné (§7)')->constrained()->cascadeOnDelete();
            $table->string('sens', 25)->comment('Sens — enum FraisSens : facturé au client | supporté par le transitaire (§7)');
            $table->string('categorie', 30)->comment('Catégorie — enum FraisCategorie (§7) : client → transit, dédouanement, manutention, transport, stockage, frais portuaires, frais administratifs, autres prestations | transitaire → transporteur, port, manutention, fournisseurs, prestataires, autres dépenses');
            $table->string('libelle')->nullable()->comment('Libellé libre');
            $table->decimal('montant', 14, 2)->comment('Montant du frais (§7)');
            $table->char('devise', 3)->comment('Devise : DZD | EUR | USD (§7/§8)');
            $table->foreignId('fournisseur_id')->nullable()->comment('Fournisseur concerné — requis si supporté par le transitaire (§7/§10)')->constrained();
            $table->date('date_frais')->comment('Date du frais');
            $table->foreignId('created_by')->nullable()->comment('Utilisateur créateur')->constrained('users');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index(['dossier_id', 'sens']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frais');
    }
};
