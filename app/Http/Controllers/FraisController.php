<?php

namespace App\Http\Controllers;

use App\Http\Requests\FraisStoreRequest;
use App\Models\Dossier;
use App\Models\Frai;
use Illuminate\Http\RedirectResponse;

class FraisController extends Controller
{
    public function store(FraisStoreRequest $request, string $numero): RedirectResponse
    {
        $this->dossier($numero)->frais()->create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.frais_enregistre'));
    }

    public function destroy(Frai $frai): RedirectResponse
    {
        $numero = $frai->dossier?->numero;
        $frai->delete();

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.frais_supprime'));
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
