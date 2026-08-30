<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Services\AuditService;
use App\Services\SoldeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        private readonly SoldeService $solde,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $clients = Client::query()
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where(fn ($q) => $q
                    ->where('raison_sociale', 'like', "%{$search}%")
                    ->orWhere('nif', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")))
            ->with('contacts')
            ->orderBy('raison_sociale')
            ->paginate($request->integer('per_page', 15));

        return response()->json($clients->through(fn (Client $client) => $this->avecSolde($client)));
    }

    public function store(ClientStoreRequest $request): JsonResponse
    {
        $client = Client::create($request->safe()->except(['contacts']) + [
            'created_by' => $request->user()->id,
        ]);

        foreach ($request->input('contacts', []) as $contact) {
            $client->contacts()->create($contact);
        }

        $this->audit->journaliser($request->user(), "Création du client {$client->raison_sociale}", null, 'client', $client->id);

        return response()->json($this->avecSolde($client->load('contacts')), 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json($this->avecSolde($client->load(['contacts', 'documents'])));
    }

    public function update(ClientUpdateRequest $request, Client $client): JsonResponse
    {
        $client->update($request->safe()->except(['contacts']));

        if ($request->has('contacts')) {
            $client->contacts()->delete();

            foreach ($request->input('contacts', []) as $contact) {
                $client->contacts()->create($contact);
            }
        }

        $this->audit->journaliser($request->user(), "Modification du client {$client->raison_sociale}", null, 'client', $client->id);

        return response()->json($this->avecSolde($client->fresh('contacts')));
    }

    public function destroy(Request $request, Client $client): JsonResponse
    {
        $client->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) du client {$client->raison_sociale}", null, 'client', $client->id);

        return response()->json(status: 204);
    }

    public function dossiers(Client $client): JsonResponse
    {
        return response()->json($client->dossiers()->orderByDesc('created_at')->paginate());
    }

    public function factures(Client $client): JsonResponse
    {
        return response()->json($client->documentsCommerciaux()->orderByDesc('numero')->paginate());
    }

    public function paiements(Client $client): JsonResponse
    {
        return response()->json($client->paiements()->orderByDesc('date_paiement')->paginate());
    }

    public function solde(Client $client): JsonResponse
    {
        return response()->json($this->solde->soldeClient($client));
    }

    private function avecSolde(Client $client): Client
    {
        $client->solde = $this->solde->soldeClient($client)['dzd'];

        return $client;
    }
}
