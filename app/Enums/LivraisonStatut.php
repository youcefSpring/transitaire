<?php

namespace App\Enums;

enum LivraisonStatut: string
{
    case Planifiee = 'planifiee';
    case EnCours = 'en_cours';
    case Livree = 'livree';
}
