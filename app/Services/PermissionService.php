<?php

namespace App\Services;

use App\Enums\UserProfile;

/**
 * Matrice des habilitations par profil métier (§14) :
 * chaque capacité liste les profils autorisés — fail-closed,
 * toute capacité inconnue est refusée à tout le monde.
 *
 * @extends array<string, list<UserProfile>>
 */
class PermissionService
{
    public const MATRICE = [
        // Clients : gestion commerciale des fiches
        'clients.gerer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::AgentCommercial,
        ],
        'clients.supprimer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
        ],

        // Dossiers de transit, marchandises, conteneurs, documents, dédouanement
        'dossiers.gerer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::AgentTransit,
        ],
        'dossiers.supprimer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
        ],

        // Frais (client et transitaire)
        'frais.gerer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::AgentTransit,
            UserProfile::Comptable,
        ],

        // Documents commerciaux : devis, factures, reçus… (§14 : un agent de
        // transit ne supprime pas une facture — il n'est pas habilité ici)
        'documents-commerciaux.gerer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::AgentCommercial,
            UserProfile::Comptable,
        ],
        'documents-commerciaux.supprimer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::AgentCommercial,
            UserProfile::Comptable,
        ],

        // Encaissements
        'paiements.gerer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::Comptable,
        ],

        // Fournisseurs et intermédiaires
        'fournisseurs.gerer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::Comptable,
        ],

        // Camions, chauffeurs, livraisons
        'transport.gerer' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
            UserProfile::ResponsableTransport,
        ],

        // Comptes utilisateurs
        'users.gerer' => [
            UserProfile::Administrateur,
        ],

        // Journal d'audit
        'audit.consulter' => [
            UserProfile::Administrateur,
            UserProfile::Directeur,
        ],
    ];

    /**
     * Vérifie qu'un profil dispose d'une capacité.
     */
    public static function autorise(UserProfile $profil, string $capacite): bool
    {
        return in_array($profil, self::MATRICE[$capacite] ?? [], true);
    }
}
