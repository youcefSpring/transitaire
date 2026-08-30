<?php

namespace App\Enums;

enum DocumentCategorie: string
{
    case FactureCommerciale = 'facture_commerciale';
    case PackingList = 'packing_list';
    case BillOfLading = 'bill_of_lading';
    case CertificatOrigine = 'certificat_origine';
    case Douanier = 'douanier';
    case Transport = 'transport';
    case Client = 'client';
    case BonLivraison = 'bon_livraison';
    case Facture = 'facture';
    case Quitance = 'quitance';
    case Autre = 'autre';
}
