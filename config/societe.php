<?php

/*
 * Identité de la société transitaire pour les papiers officiels (PDF).
 * Surchargée par les variables d'environnement (fichier .env).
 */
return [
    'raison_sociale' => env('SOCIETE_RAISON_SOCIALE', 'Transiatire Transit & Freight'),
    'forme_juridique' => env('SOCIETE_FORME_JURIDIQUE', 'SARL — Commissionnaire agréé en douane'),
    'adresse' => env('SOCIETE_ADRESSE', '12, Quai du Port Commercial'),
    'wilaya' => env('SOCIETE_WILAYA', 'Alger 16000, Algérie'),
    'telephone' => env('SOCIETE_TELEPHONE', '+213 21 00 00 00'),
    'email' => env('SOCIETE_EMAIL', 'contact@transiatire.dz'),

    'nif' => env('SOCIETE_NIF', '000000000000000'),
    'nis' => env('SOCIETE_NIS', '000000000000000'),
    'rc' => env('SOCIETE_RC', '16/00-0000000B16'),
    'ai' => env('SOCIETE_AI', '00000000000'),

    'banque' => env('SOCIETE_BANQUE', '—'),
    'rib' => env('SOCIETE_RIB', '000 0000 0000 0000 0000 00'),
];
