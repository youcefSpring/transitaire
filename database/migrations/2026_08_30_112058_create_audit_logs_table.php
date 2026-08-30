<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('user_id')->comment('Utilisateur — qui (§15)')->constrained();
            $table->string('action')->comment('Action effectuée (§15), ex. : Modification du statut du dossier #TR-2026-0158');
            $table->foreignId('dossier_id')->nullable()->comment('Dossier concerné (§15)')->constrained()->nullOnDelete();
            $table->string('entite_type', 50)->nullable()->comment("Type d'entité hors dossier [PROPOSÉ]");
            $table->unsignedBigInteger('entite_id')->nullable()->comment("Identifiant de l'entité hors dossier [PROPOSÉ]");
            $table->text('details')->nullable()->comment('Détails complémentaires');
            $table->timestamp('created_at')->comment("Date et heure de l'action (§15) — journal append-only, ni update ni suppression");
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
