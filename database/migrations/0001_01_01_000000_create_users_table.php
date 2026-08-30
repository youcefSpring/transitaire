<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Identifiant interne');
            $table->string('name')->comment("Nom complet de l'utilisateur (§14)");
            $table->string('email')->unique()->comment('Adresse email unique (§14)');
            $table->timestamp('email_verified_at')->nullable()->comment('Vérification email (infrastructure Laravel)');
            $table->string('password')->comment('Mot de passe haché');
            $table->string('profile', 30)->comment('Profil (§14) — enum code UserProfile : administrateur, directeur, agent de transit, agent commercial, comptable, responsable transport, consultation (ADR-13 : aucune table de rôles)');
            $table->boolean('is_active')->default(true)->comment('Compte actif');
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable()->comment('Suppression logique (ADR-02)');
            $table->index('profile');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
