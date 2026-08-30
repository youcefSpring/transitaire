<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('documentable_type', 50)->comment('Entité porteuse : dossier | client (§5, polymorphe)');
            $table->unsignedBigInteger('documentable_id')->comment("Identifiant de l'entité porteuse (§5)");
            $table->string('categorie', 50)->comment("Catégorie — enum DocumentCategorie : facture commerciale, packing list, bill of lading, certificat d'origine, douanier, transport, client, bon de livraison, facture, quittance, autre (§5)");
            $table->string('nom_original')->comment('Nom original du fichier téléversé (§5)');
            $table->string('chemin', 512)->comment('Chemin de stockage du fichier (§5)');
            $table->string('mime_type', 100)->comment('Type MIME du fichier');
            $table->unsignedInteger('taille')->comment('Taille en octets');
            $table->foreignId('televerse_par')->comment('Téléversé par (§5)')->constrained('users');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
