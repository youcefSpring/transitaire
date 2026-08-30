<?php

namespace Database\Seeders;

use App\Enums\UserProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Un utilisateur par profil (§14, ADR-13 : aucune table de rôles).
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $comptes = [
            [UserProfile::Administrateur, 'Admin Système', 'admin@transitaire.dz'],
            [UserProfile::Directeur, 'Karim Belhadj', 'directeur@transitaire.dz'],
            [UserProfile::AgentTransit, 'Sofiane Meziane', 'transit@transitaire.dz'],
            [UserProfile::AgentCommercial, 'Nadia Haddad', 'commercial@transitaire.dz'],
            [UserProfile::Comptable, 'Yacine Cherif', 'comptable@transitaire.dz'],
            [UserProfile::ResponsableTransport, 'Rachid Amrani', 'transport@transitaire.dz'],
            [UserProfile::Consultation, 'Invité Consultation', 'consultation@transitaire.dz'],
        ];

        foreach ($comptes as [$profile, $nom, $email]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nom,
                    'password' => Hash::make('password'),
                    'profile' => $profile,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
