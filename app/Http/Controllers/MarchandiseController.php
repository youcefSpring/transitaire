<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarchandiseStoreRequest;
use App\Models\Dossier;
use App\Models\Marchandise;
use Illuminate\Http\RedirectResponse;

class MarchandiseController extends Controller
{
    public function store(MarchandiseStoreRequest $request, string $numero): RedirectResponse
    {
        $this->dossier($numero)->marchandises()->create($request->validated());

        return redirect()->route('dossiers.show', $numero)->with('message', 'Marchandise ajoutée.');
    }

    public function update(MarchandiseStoreRequest $request, string $numero, Marchandise $marchandise): RedirectResponse
    {
        $marchandise->update($request->validated());

        return redirect()->route('dossiers.show', $numero)->with('message', 'Marchandise mise à jour.');
    }

    public function destroy(string $numero, Marchandise $marchandise): RedirectResponse
    {
        $marchandise->delete();

        return redirect()->route('dossiers.show', $numero)->with('message', 'Marchandise supprimée.');
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
