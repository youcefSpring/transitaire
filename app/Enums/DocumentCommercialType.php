<?php

namespace App\Enums;

enum DocumentCommercialType: string
{
    case Devis = 'devis';
    case BonCommande = 'bon_commande';
    case Facture = 'facture';
    case Avoir = 'avoir';
    case Recu = 'recu';
    case SituationClient = 'situation_client';
}
