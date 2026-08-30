<?php

namespace App\Enums;

enum AlerteType: string
{
    case ArriveeNavire = 'arrivee_navire';
    case DossierIncomplet = 'dossier_incomplet';
    case DocumentManquant = 'document_manquant';
    case DossierBloque = 'dossier_bloque';
    case FactureImpayee = 'facture_impayee';
    case LivraisonAEffectuer = 'livraison_a_effectuer';
    case ConteneurARetourner = 'conteneur_a_retourner';
    case EcheanceImportante = 'echeance_importante';
    case RetardDossier = 'retard_dossier';
}
