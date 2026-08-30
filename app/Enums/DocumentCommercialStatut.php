<?php

namespace App\Enums;

enum DocumentCommercialStatut: string
{
    case Brouillon = 'brouillon';
    case Emis = 'emis';
    case Envoye = 'envoye';
    case Accepte = 'accepte';
    case Refuse = 'refuse';
    case Confirme = 'confirme';
    case PartiellementPaye = 'partiellement_paye';
    case Paye = 'paye';
    case Annule = 'annule';
}
