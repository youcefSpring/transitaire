<?php

namespace App\Enums;

enum FournisseurType: string
{
    case Transporteur = 'transporteur';
    case CompagnieMaritime = 'compagnie_maritime';
    case CompagnieAerienne = 'compagnie_aerienne';
    case Prestataire = 'prestataire';
    case Manutention = 'manutention';
    case Entrepot = 'entrepot';
    case Autre = 'autre';
}
