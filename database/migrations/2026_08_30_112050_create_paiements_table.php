<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('client_id')->comment('Client concerné (§9)')->constrained();
            $table->foreignId('document_id')->nullable()->comment('Facture / avis concerné — nullable : versement (§9)')->constrained('documents_commerciaux');
            $table->string('mode', 15)->comment('Mode — enum PaiementMode : espèces, chèque, virement, versement, autre (§9)');
            $table->decimal('montant', 14, 2)->comment('Montant payé (§9)');
            $table->char('devise', 3)->comment('Devise : DZD | EUR | USD (§9)');
            $table->date('date_paiement')->comment('Date du paiement (§9)');
            $table->string('reference', 100)->nullable()->comment('Référence chèque / virement [PROPOSÉ G-27]');
            $table->foreignId('created_by')->comment('Utilisateur enregistreur (§9)')->constrained('users');
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->timestamps();
            $table->index('date_paiement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
