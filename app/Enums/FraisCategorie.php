<?php

namespace App\Enums;

enum FraisCategorie: string
{
    case Transit = 'transit';
    case Dedouanement = 'dedouanement';
    case Manutention = 'manutention';
    case Transport = 'transport';
    case Stockage = 'stockage';
    case FraisPortuaires = 'frais_portuaires';
    case FraisAdministratifs = 'frais_administratifs';
    case AutresPrestations = 'autres_prestations';
    case Transporteur = 'transporteur';
    case Port = 'port';
    case Fournisseurs = 'fournisseurs';
    case Prestataires = 'prestataires';
    case AutresDepenses = 'autres_depenses';
}
