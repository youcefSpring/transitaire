<?php

namespace App\Enums;

enum ConteneurStatut: string
{
    case EnAttente = 'en_attente';
    case Sorti = 'sorti';
    case Livre = 'livre';
    case Retourne = 'retourne';
}
