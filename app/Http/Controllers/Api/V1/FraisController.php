<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FraisStoreRequest;
use App\Models\Dossier;
use App\Models\Frai;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FraisController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request, string $numero): JsonResponse
    {
        $frais = $this->dossier($numero)->frais()->with('fournisseur');

        if ($request->query('sens')) {
            $frais->where('sens', $request->query('sens'));
        }

        return response()->json($frais->orderByDesc('date_frais')->get());
    }

    public function store(FraisStoreRequest $request, string $numero): JsonResponse
    {
        $dossier = $this->dossier($numero);

        $frai = $dossier->frais()->create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        $this->audit->journaliser(
            $request->user(),
            "Ajout du frais {$frai->categorie->value} ({$frai->montant} {$frai->devise->value}) au dossier #{$dossier->numero}",
            $dossier,
        );

        return response()->json($frai->load('fournisseur'), 201);
    }

    public function destroy(Request $request, Frai $frai): JsonResponse
    {
        $frai->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) du frais #{$frai->id}", $frai->dossier);

        return response()->json(status: 204);
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
