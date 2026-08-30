<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->foreignId('client_id')->comment('Client concerné (§1)')->constrained()->cascadeOnDelete();
            $table->string('nom')->comment('Nom du contact (§1)');
            $table->string('fonction')->nullable()->comment('Fonction [PROPOSÉ]');
            $table->string('telephone', 20)->comment('Téléphone du contact (§1)');
            $table->string('email')->nullable()->comment('Email du contact (§1)');
            $table->text('notes')->nullable()->comment('Notes libres');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_contacts');
    }
};
