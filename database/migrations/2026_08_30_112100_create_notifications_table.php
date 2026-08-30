<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('canal', 15)->comment('Canal — enum NotificationCanal : email | SMS | WhatsApp (§17)');
            $table->string('destinataire')->comment('Destinataire (§17)');
            $table->foreignId('client_id')->nullable()->comment('Client destinataire (§17)')->constrained()->nullOnDelete();
            $table->string('sujet')->nullable()->comment('Sujet du message');
            $table->text('message')->comment('Corps du message (§17)');
            $table->string('statut', 15)->default('en_file')->comment('Statut — enum NotificationStatut [PROPOSÉ G-10/G-12]');
            $table->dateTime('envoyee_le')->nullable()->comment("Date et heure d'envoi");
            $table->timestamp('created_at')->nullable()->comment('Créé le');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
