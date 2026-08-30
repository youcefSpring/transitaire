<?php

namespace App\Enums;

enum AlerteStatut: string
{
    case Nouvelle = 'nouvelle';
    case Lue = 'lue';
    case Traitee = 'traitee';
}
