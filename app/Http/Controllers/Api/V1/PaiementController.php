<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaiementStoreRequest;
use App\Models\Client;
use App\Models\DocumentCommercial;
use App\Models\Paiement;
use App\Services\AuditService;
use App\Services\SoldeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function __construct(
        private readonly SoldeService $solde,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if ($request->boolean('impayees')) {
            $factures = DocumentCommercialType::Facture;

            $impayees = DocumentCommercial::query()
                ->with('client')
                ->where('type', $factures->value)
                ->whereIn('statut', [DocumentCommercialStatut::Emis->value, DocumentCommercialStatut::PartiellementPaye->value])
                ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
                ->get()
                ->filter(fn ($facture) => $this->solde->resteParFacture($facture) > 0)
                ->map(fn ($facture) => [
                    'numero' => $facture->numero,
                    'client' => $facture->client?->raison_sociale,
                    'echeance' => $facture->date_echeance?->format('d/m/Y'),
                    'reste_a_payer' => $this->solde->resteParFacture($facture),
                ])
                ->values();

            return response()->json(['data' => $impayees]);
        }

        $paiements = Paiement::query()
            ->with('client')
            ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->orderByDesc('date_paiement')
            ->paginate($request->integer('per_page', 15));

        return response()->json($paiements);
    }

    public function store(PaiementStoreRequest $request): JsonResponse
    {
        $paiement = Paiement::create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        if ($paiement->document !== null) {
            $reste = $this->solde->resteParFacture($paiement->document);
            $paiement->document->update([
                'statut' => $reste <= 0
                    ? DocumentCommercialStatut::Paye
                    : DocumentCommercialStatut::PartiellementPaye,
            ]);
        }

        $this->audit->journaliser(
            $request->user(),
            "Enregistrement du paiement {$paiement->montant} {$paiement->devise->value} ({$paiement->mode->value})",
            null,
            'paiement',
            $paiement->id,
        );

        return response()->json($paiement->load('client'), 201);
    }

    public function solde(Client $client): JsonResponse
    {
        return response()->json($this->solde->soldeClient($client));
    }
}
