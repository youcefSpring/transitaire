<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConteneurStoreRequest;
use App\Http\Requests\ConteneurUpdateRequest;
use App\Models\Client;
use App\Models\Conteneur;
use App\Models\Dossier;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConteneurController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $conteneurs = Conteneur::query()
            ->with('dossier')
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where(fn ($q) => $q
                    ->where('numero', 'like', "%{$search}%")
                    ->orWhere('numero_bl', 'like', "%{$search}%")))
            ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->orderBy('date_eta')
            ->paginate(15)
            ->withQueryString();

        return view('conteneurs.index', [
            'conteneurs' => $conteneurs,
            'clients' => Client::orderBy('raison_sociale')->get(),
            'dossiers' => Dossier::orderByDesc('created_at')->get(['id', 'numero']),
        ]);
    }

    public function store(ConteneurStoreRequest $request): RedirectResponse
    {
        $conteneur = Conteneur::create($request->validated());

        $this->audit->journaliser($request->user(), "Enregistrement du conteneur {$conteneur->numero}", $conteneur->dossier, 'conteneur', $conteneur->id);

        return redirect()->route('conteneurs.index')->with('message', __('app.messages.conteneur_enregistre'));
    }

    public function update(ConteneurUpdateRequest $request, Conteneur $conteneur): RedirectResponse
    {
        $conteneur->update($request->validated());

        $this->audit->journaliser($request->user(), "Modification du conteneur {$conteneur->numero}", $conteneur->dossier, 'conteneur', $conteneur->id);

        return redirect()->route('conteneurs.index')->with('message', __('app.messages.conteneur_mis_a_jour'));
    }

    public function destroy(Conteneur $conteneur): RedirectResponse
    {
        $conteneur->delete();

        $this->audit->journaliser(auth()->user(), "Suppression (logique) du conteneur {$conteneur->numero}", $conteneur->dossier, 'conteneur', $conteneur->id);

        return redirect()->route('conteneurs.index')->with('message', __('app.messages.conteneur_supprime'));
    }
}
