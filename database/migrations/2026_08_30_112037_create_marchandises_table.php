<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marchandises', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('dossier_id')->comment('Dossier de transit concerné (§3)')->constrained()->cascadeOnDelete();
            $table->string('designation')->comment('Désignation de la marchandise (§3)');
            $table->decimal('quantite', 12, 3)->comment('Quantité (§3)');
            $table->string('unite', 50)->comment('Unité (§3)');
            $table->unsignedInteger('nombre_colis')->comment('Nombre de colis (§3)');
            $table->decimal('poids', 12, 3)->comment('Poids (§3)');
            $table->decimal('volume', 12, 3)->comment('Volume (§3)');
            $table->decimal('valeur', 14, 2)->comment('Valeur (§3) — devise du dossier');
            $table->string('pays_origine', 100)->comment("Pays d'origine (§3)");
            $table->string('code_tarifaire', 50)->comment('Code tarifaire / nomenclature douanière (§3)');
            $table->text('infos_complementaires')->nullable()->comment('Informations complémentaires (§3)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marchandises');
    }
};
