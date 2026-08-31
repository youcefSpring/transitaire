<?php

namespace App\Http\Controllers;

use App\Enums\DouaneEtape;
use App\Http\Requests\DouaneEtapeRequest;
use App\Models\Dossier;
use App\Services\DouaneService;
use Illuminate\Http\RedirectResponse;

class DedouanementController extends Controller
{
    public function __construct(
        private readonly DouaneService $douane,
    ) {}

    public function store(DouaneEtapeRequest $request, string $numero): RedirectResponse
    {
        $this->douane->enregistrerEtape(
            $this->dossier($numero),
            $request->enum('etape', DouaneEtape::class),
            $request->user(),
        );

        return redirect()->route('dossiers.show', $numero)->with('message', __('app.messages.etape_douaniere_enregistree'));
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
