<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lignes_prestations', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('document_id')->comment('Document commercial lié (§8)')->constrained('documents_commerciaux')->cascadeOnDelete();
            $table->string('designation')->comment('Désignation de la prestation (§8)');
            $table->string('categorie', 30)->comment('Catégorie de prestation — enum FraisCategorie côté client (§7)');
            $table->decimal('quantite', 12, 3)->comment('Quantité');
            $table->decimal('prix_unitaire', 14, 2)->comment('Prix unitaire');
            $table->decimal('montant', 14, 2)->comment('Montant = quantité × prix unitaire');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_prestations');
    }
};
