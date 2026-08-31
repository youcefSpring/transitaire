<?php

namespace App\Http\Controllers;

use App\Enums\DouaneEtape;
use App\Http\Requests\DouaneEtapeRequest;
use App\Models\Dossier;
use App\Services\AuditService;
use App\Services\DouaneService;
use Illuminate\Http\RedirectResponse;

class DedouanementController extends Controller
{
    public function __construct(
        private readonly DouaneService $douane,
        private readonly AuditService $audit,
    ) {}

    public function store(DouaneEtapeRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);

        $etape = $this->douane->enregistrerEtape(
            $dossier,
            $request->enum('etape', DouaneEtape::class),
            $request->user(),
        );

        $this->audit->journaliser(
            $request->user(),
            "Étape douanière « {$etape->etape->value} » enregistrée sur le dossier #{$dossier->numero}",
            $dossier,
            'dedouanement_etape',
            $etape->id,
        );

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.etape_douaniere_enregistree'));
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
