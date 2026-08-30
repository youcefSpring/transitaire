<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DossierStatut;
use App\Http\Controllers\Controller;
use App\Http\Requests\DossierBlocageRequest;
use App\Http\Requests\DossierStatutRequest;
use App\Http\Requests\DossierStoreRequest;
use App\Http\Requests\DossierUpdateRequest;
use App\Models\Dossier;
use App\Services\AuditService;
use App\Services\DossierService;
use App\Services\MargeService;
use App\Services\NumerotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DossierController extends Controller
{
    public function __construct(
        private readonly NumerotationService $numeros,
        private readonly DossierService $dossiers,
        private readonly MargeService $marges,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $dossiers = Dossier::query()
            ->with('client')
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($request->boolean('bloque'), fn ($query) => $query->where('bloque', true))
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json($dossiers);
    }

    public function store(DossierStoreRequest $request): JsonResponse
    {
        $dossier = Dossier::create($request->validated() + [
            'numero' => $this->numeros->prochainNumeroDossier(),
            'created_by' => $request->user()->id,
        ]);

        $this->audit->journaliser($request->user(), "Création du dossier #{$dossier->numero}", $dossier);

        return response()->json($dossier, 201);
    }

    public function show(string $numero): JsonResponse
    {
        return response()->json($this->dossier($numero)->load([
            'client.contacts',
            'marchandises',
            'conteneurs',
            'documents',
            'douaneEtapes.executedBy',
            'frais.fournisseur',
            'documentsCommerciaux.lignes',
            'livraisons.camion',
            'livraisons.chauffeur',
            'livraisons.transporteurExterne',
            'createur',
        ]));
    }

    public function update(DossierUpdateRequest $request, string $numero): JsonResponse
    {
        $dossier = $this->dossier($numero);
        $dossier->update($request->validated());

        $this->audit->journaliser($request->user(), "Modification du dossier #{$dossier->numero}", $dossier);

        return response()->json($dossier->fresh());
    }

    public function destroy(Request $request, string $numero): JsonResponse
    {
        $dossier = $this->dossier($numero);
        $dossier->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) du dossier #{$dossier->numero}", $dossier);

        return response()->json(status: 204);
    }

    public function statut(DossierStatutRequest $request, string $numero): JsonResponse
    {
        $dossier = $this->dossiers->changerStatut($this->dossier($numero), $request->enum('statut', DossierStatut::class), $request->user());

        return response()->json($dossier);
    }

    public function blocage(DossierBlocageRequest $request, string $numero): JsonResponse
    {
        $dossier = $this->dossier($numero);

        $dossier = $request->boolean('bloque')
            ? $this->dossiers->bloquer($dossier, (string) $request->input('raison'), $request->user())
            : $this->dossiers->debloquer($dossier, $request->user());

        return response()->json($dossier);
    }

    public function marge(string $numero): JsonResponse
    {
        return response()->json($this->marges->margeDossier($this->dossier($numero)));
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
