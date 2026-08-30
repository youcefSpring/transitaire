<?php

namespace App\Enums;

enum DouaneEtape: string
{
    case Declaration = 'declaration';
    case Depot = 'depot';
    case ControleDocumentaire = 'controle_documentaire';
    case Visite = 'visite';
    case Liquidation = 'liquidation';
    case Paiement = 'paiement';
    case Mainlevee = 'mainlevee';
    case Sortie = 'sortie';
}
