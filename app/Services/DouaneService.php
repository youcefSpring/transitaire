<?php

namespace App\Services;

use App\Enums\DouaneEtape;
use App\Models\DedouanementEtape;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DouaneService
{
    private const OBLIGATOIRES = [
        DouaneEtape::Declaration,
        DouaneEtape::Depot,
        DouaneEtape::ControleDocumentaire,
        DouaneEtape::Liquidation,
        DouaneEtape::Paiement,
        DouaneEtape::Mainlevee,
        DouaneEtape::Sortie,
    ];

    public function __construct(
        private readonly AuditService $audit,
        private readonly DossierService $dossiers,
    ) {}

    public function enregistrerEtape(Dossier $dossier, DouaneEtape $etape, User $user): DedouanementEtape
    {
        $existantes = $dossier->douaneEtapes()->get()->pluck('etape')->all();

        if (in_array($etape, $existantes, true)) {
            throw ValidationException::withMessages([
                'etape' => "L'étape {$etape->value} est déjà enregistrée pour le dossier {$dossier->numero}.",
            ]);
        }

        foreach ($this->prealables($etape) as $prealable) {
            if (! in_array($prealable, $existantes, true)) {
                throw ValidationException::withMessages([
                    'etape' => "L'étape {$prealable->value} doit être enregistrée avant {$etape->value} (§6).",
                ]);
            }
        }

        $enregistree = DB::transaction(function () use ($dossier, $etape, $user): DedouanementEtape {
            $etapeRecord = DedouanementEtape::create([
                'dossier_id' => $dossier->id,
                'etape' => $etape,
                'executed_by' => $user->id,
                'executed_at' => now(),
            ]);

            $this->dossiers->synchroniserStatutDouane($dossier);

            return $etapeRecord;
        });

        $this->audit->journaliser(
            $user,
            "Étape douanière {$etape->value} enregistrée pour le dossier #{$dossier->numero}",
            $dossier,
        );

        return $enregistree;
    }

    private function prealables(DouaneEtape $etape): array
    {
        $position = array_search($etape, self::OBLIGATOIRES, true);

        return $position === false
            ? array_slice(self::OBLIGATOIRES, 0, 3)
            : array_slice(self::OBLIGATOIRES, 0, (int) $position);
    }
}
