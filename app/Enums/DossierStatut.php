<?php

namespace App\Enums;

enum DossierStatut: string
{
    case Nouveau = 'nouveau';
    case DocumentsRecus = 'documents_recus';
    case EnCours = 'en_cours';
    case Dedouanement = 'dedouanement';
    case DouaneTerminee = 'douane_terminee';
    case Livraison = 'livraison';
    case Cloture = 'cloture';
}
