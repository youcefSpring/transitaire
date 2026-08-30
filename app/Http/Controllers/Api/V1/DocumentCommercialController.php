<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Enums\FraisSens;
use App\Enums\UserProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentCommercialStatutRequest;
use App\Http\Requests\DocumentCommercialStoreRequest;
use App\Models\DocumentCommercial;
use App\Models\Frai;
use App\Services\AuditService;
use App\Services\NumerotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentCommercialController extends Controller
{
    public function __construct(
        private readonly NumerotationService $numeros,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $documents = DocumentCommercial::query()
            ->with('client')
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($request->query('dossier'), fn ($query, $numero) => $query
                ->whereHas('dossier', fn ($q) => $q->where('numero', $numero)))
            ->orderByDesc('numero')
            ->paginate($request->integer('per_page', 15));

        return response()->json($documents);
    }

    public function store(DocumentCommercialStoreRequest $request): JsonResponse
    {
        $type = $request->enum('type', DocumentCommercialType::class);

        $totalPrestations = collect($request->input('lignes'))
            ->sum(fn (array $ligne) => (float) $ligne['quantite'] * (float) $ligne['prix_unitaire']);

        $totalFrais = $request->filled('dossier_id')
            ? (float) Frai::query()
                ->where('dossier_id', $request->input('dossier_id'))
                ->where('sens', FraisSens::FactureClient->value)
                ->where('devise', $request->input('devise'))
                ->sum('montant')
            : 0.0;

        $document = DocumentCommercial::create($request->safe()->except(['lignes']) + [
            'numero' => $this->numeros->prochainNumeroDocument($type),
            'total_prestations' => round($totalPrestations, 2),
            'total_frais' => round($totalFrais, 2),
            'montant_total' => round($totalPrestations + $totalFrais
                + (float) $request->input('total_taxes')
                - (float) $request->input('remise'), 2),
            'created_by' => $request->user()->id,
        ]);

        foreach ($request->input('lignes') as $ligne) {
            $document->lignes()->create($ligne + [
                'montant' => round((float) $ligne['quantite'] * (float) $ligne['prix_unitaire'], 2),
            ]);
        }

        $this->audit->journaliser($request->user(), "Création du document commercial {$document->numero}", $document->dossier, 'document_commercial', $document->id);

        return response()->json($document->load(['lignes', 'client']), 201);
    }

    public function show(DocumentCommercial $documentCommercial): JsonResponse
    {
        return response()->json($documentCommercial->load(['client', 'dossier', 'lignes', 'paiements']));
    }

    public function statut(DocumentCommercialStatutRequest $request, DocumentCommercial $documentCommercial): JsonResponse
    {
        $documentCommercial->update(['statut' => $request->enum('statut', DocumentCommercialStatut::class)]);

        $this->audit->journaliser($request->user(), "Passage du document {$documentCommercial->numero} au statut {$documentCommercial->statut->value}", $documentCommercial->dossier, 'document_commercial', $documentCommercial->id);

        return response()->json($documentCommercial);
    }

    public function destroy(Request $request, DocumentCommercial $documentCommercial): JsonResponse
    {
        if ($documentCommercial->type === DocumentCommercialType::Facture
            && $request->user()?->profile === UserProfile::AgentTransit) {
            abort(403, 'Un agent de transit ne peut pas supprimer une facture (§14).');
        }

        $documentCommercial->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) du document {$documentCommercial->numero}", $documentCommercial->dossier, 'document_commercial', $documentCommercial->id);

        return response()->json(status: 204);
    }
}
