<?php

namespace App\Enums;

enum PaiementMode: string
{
    case Especes = 'especes';
    case Cheque = 'cheque';
    case Virement = 'virement';
    case Versement = 'versement';
    case Autre = 'autre';
}
