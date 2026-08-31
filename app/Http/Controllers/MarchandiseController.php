<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarchandiseStoreRequest;
use App\Models\Dossier;
use App\Models\Marchandise;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;

class MarchandiseController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function store(MarchandiseStoreRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);

        $marchandise = $dossier->marchandises()->create($request->validated());

        $this->audit->journaliser($request->user(), "Ajout de la marchandise « {$marchandise->designation} » au dossier #{$dossier->numero}", $dossier, 'marchandise', $marchandise->id);

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.marchandise_ajoutee'));
    }

    public function update(MarchandiseStoreRequest $request, string $numero, Marchandise $marchandise): RedirectResponse
    {
        $marchandise->update($request->validated());

        $this->audit->journaliser($request->user(), "Modification de la marchandise « {$marchandise->designation} » du dossier #{$numero}", $marchandise->dossier, 'marchandise', $marchandise->id);

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.marchandise_mise_a_jour'));
    }

    public function destroy(string $numero, Marchandise $marchandise): RedirectResponse
    {
        $marchandise->delete();

        $this->audit->journaliser(auth()->user(), "Suppression de la marchandise « {$marchandise->designation} » du dossier #{$numero}", $marchandise->dossier, 'marchandise', $marchandise->id);

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.marchandise_supprimee'));
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
