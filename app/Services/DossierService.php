<?php

namespace App\Services;

use App\Enums\DossierStatut;
use App\Enums\DouaneEtape;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class DossierService
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function changerStatut(Dossier $dossier, DossierStatut $cible, ?User $user = null): Dossier
    {
        if ($dossier->bloque) {
            throw ValidationException::withMessages([
                'statut' => __('app.messages.dossier_bloque_erreur', [
                    'numero' => $dossier->numero,
                    'raison' => $dossier->raison_blocage,
                ]),
            ]);
        }

        if ($this->transitionSuivante($dossier->statut) !== $cible) {
            throw ValidationException::withMessages([
                'statut' => __('app.messages.transition_non_autorisee', [
                    'de' => __("app.statut.{$dossier->statut->value}"),
                    'vers' => __("app.statut.{$cible->value}"),
                ]),
            ]);
        }

        $dossier->forceFill(['statut' => $cible])->save();

        if ($user !== null) {
            $this->audit->journaliser(
                $user,
                "Modification du statut du dossier #{$dossier->numero} : {$cible->value}",
                $dossier,
            );
        }

        return $dossier;
    }

    public function bloquer(Dossier $dossier, string $raison, ?User $user = null): Dossier
    {
        $raison = trim($raison);

        if ($raison === '') {
            throw ValidationException::withMessages([
                'raison_blocage' => __('app.messages.raison_blocage_requise'),
            ]);
        }

        $dossier->forceFill([
            'bloque' => true,
            'raison_blocage' => $raison,
        ])->save();

        if ($user !== null) {
            $this->audit->journaliser($user, "Blocage du dossier #{$dossier->numero} : {$raison}", $dossier);
        }

        return $dossier;
    }

    public function debloquer(Dossier $dossier, ?User $user = null): Dossier
    {
        $dossier->forceFill([
            'bloque' => false,
            'raison_blocage' => null,
        ])->save();

        if ($user !== null) {
            $this->audit->journaliser($user, "Déblocage du dossier #{$dossier->numero}", $dossier);
        }

        return $dossier;
    }

    public function synchroniserStatutDouane(Dossier $dossier): Dossier
    {
        $etapes = $dossier->douaneEtapes()->get()->pluck('etape')->all();

        $cible = match (true) {
            in_array(DouaneEtape::Mainlevee, $etapes, true)
                && in_array(DouaneEtape::Sortie, $etapes, true) => DossierStatut::DouaneTerminee,
            in_array(DouaneEtape::Declaration, $etapes, true) => DossierStatut::Dedouanement,
            default => null,
        };

        if ($cible !== null && $cible !== $dossier->statut && ! $dossier->bloque) {
            $dossier->forceFill(['statut' => $cible])->save();
        }

        return $dossier;
    }

    private function transitionSuivante(DossierStatut $statut): ?DossierStatut
    {
        return match ($statut) {
            DossierStatut::Nouveau => DossierStatut::DocumentsRecus,
            DossierStatut::DocumentsRecus => DossierStatut::EnCours,
            DossierStatut::EnCours => DossierStatut::Dedouanement,
            DossierStatut::Dedouanement => DossierStatut::DouaneTerminee,
            DossierStatut::DouaneTerminee => DossierStatut::Livraison,
            DossierStatut::Livraison => DossierStatut::Cloture,
            default => null,
        };
    }
}
