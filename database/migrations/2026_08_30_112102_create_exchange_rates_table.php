<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->char('devise', 3)->comment('Devise source : EUR | USD — DZD = pivot implicite (G-06 DÉCIDÉ)');
            $table->decimal('taux_dzd', 14, 6)->comment('Taux : 1 devise = X DZD — saisie journalière manuelle (G-06 DÉCIDÉ)');
            $table->date('date_taux')->comment('Date de validité du taux — un taux par devise et par jour');
            $table->foreignId('created_by')->comment('Saisi par (comptable)')->constrained('users');
            $table->timestamps();
            $table->unique(['devise', 'date_taux']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
