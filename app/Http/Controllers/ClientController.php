<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Services\AuditService;
use App\Services\SoldeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(
        private readonly SoldeService $solde,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $clients = Client::query()
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where('raison_sociale', 'like', "%{$search}%")
                ->orWhere('nif', 'like', "%{$search}%"))
            ->with('contacts')
            ->orderBy('raison_sociale')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Client $client) => tap($client, fn () => $client->solde = $this->solde->soldeClient($client)['dzd']));

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(ClientStoreRequest $request): RedirectResponse
    {
        $client = Client::create($request->safe()->except(['contacts']) + [
            'created_by' => $request->user()->id,
        ]);

        foreach ($request->input('contacts', []) as $contact) {
            $client->contacts()->create($contact);
        }

        $this->audit->journaliser($request->user(), "Création du client {$client->raison_sociale}", null, 'client', $client->id);

        return redirect()->route('clients.show', $client)->with('message', __('app.messages.client_cree'));
    }

    public function show(Client $client): View
    {
        $client->load(['contacts', 'documents']);

        $solde = $this->solde->soldeClient($client);

        return view('clients.show', compact('client', 'solde'));
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(ClientUpdateRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->safe()->except(['contacts']));

        if ($request->has('contacts')) {
            $client->contacts()->delete();

            foreach ($request->input('contacts', []) as $contact) {
                $client->contacts()->create($contact);
            }
        }

        $this->audit->journaliser($request->user(), "Modification du client {$client->raison_sociale}", null, 'client', $client->id);

        return redirect()->route('clients.show', $client)->with('message', __('app.messages.client_mis_a_jour'));
    }

    public function destroy(Request $request, Client $client): RedirectResponse
    {
        $client->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) du client {$client->raison_sociale}", null, 'client', $client->id);

        return redirect()->route('clients.index')->with('message', __('app.messages.client_supprime'));
    }
}
