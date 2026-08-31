<?php

namespace App\Http\Controllers;

use App\Http\Requests\FraisStoreRequest;
use App\Models\Dossier;
use App\Models\Frai;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;

class FraisController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function store(FraisStoreRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);

        $frai = $dossier->frais()->create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        $this->audit->journaliser(
            $request->user(),
            "Enregistrement du frais {$frai->montant} {$frai->devise->value} ({$frai->categorie->value}) sur le dossier #{$dossier->numero}",
            $dossier,
            'frai',
            $frai->id,
        );

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.frais_enregistre'));
    }

    public function destroy(Frai $frai): RedirectResponse
    {
        $numero = $frai->dossier?->numero;
        $frai->delete();

        $this->audit->journaliser(auth()->user(), "Suppression (logique) du frais #{$frai->id}", $frai->dossier, 'frai', $frai->id);

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.frais_supprime'));
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
