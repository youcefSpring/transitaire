<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Enums\FraisSens;
use App\Enums\UserProfile;
use App\Http\Requests\DocumentCommercialStatutRequest;
use App\Http\Requests\DocumentCommercialStoreRequest;
use App\Models\Client;
use App\Models\DocumentCommercial;
use App\Models\Dossier;
use App\Models\Frai;
use App\Services\AuditService;
use App\Services\NumerotationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentCommercialController extends Controller
{
    public function __construct(
        private readonly NumerotationService $numeros,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $documents = DocumentCommercial::query()
            ->with('client')
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where('numero', 'like', "%{$search}%"))
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->orderByDesc('numero')
            ->paginate(15)
            ->withQueryString();

        return view('factures.index', [
            'documents' => $documents,
            'clients' => Client::orderBy('raison_sociale')->get(['id', 'raison_sociale']),
        ]);
    }

    public function create(): View
    {
        return view('factures.create', [
            'clients' => Client::orderBy('raison_sociale')->get(),
            'dossiers' => Dossier::orderByDesc('created_at')->get(['id', 'numero']),
        ]);
    }

    public function store(DocumentCommercialStoreRequest $request): RedirectResponse
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

        return redirect()->route('documents-commerciaux.show', $document)->with('message', "Document {$document->numero} créé.");
    }

    public function show(DocumentCommercial $documentCommercial): View
    {
        return view('factures.show', ['document' => $documentCommercial->load(['client', 'dossier', 'lignes', 'paiements'])]);
    }

    public function statut(DocumentCommercialStatutRequest $request, DocumentCommercial $documentCommercial): RedirectResponse
    {
        $documentCommercial->update(['statut' => $request->enum('statut', DocumentCommercialStatut::class)]);

        $this->audit->journaliser($request->user(), "Passage du document {$documentCommercial->numero} au statut {$documentCommercial->statut->value}", $documentCommercial->dossier, 'document_commercial', $documentCommercial->id);

        return back()->with('message', 'Statut mis à jour.');
    }

    public function destroy(Request $request, DocumentCommercial $documentCommercial): RedirectResponse
    {
        if ($documentCommercial->type === DocumentCommercialType::Facture
            && $request->user()?->profile === UserProfile::AgentTransit) {
            abort(403, 'Un agent de transit ne peut pas supprimer une facture (§14).');
        }

        $documentCommercial->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) du document {$documentCommercial->numero}", $documentCommercial->dossier, 'document_commercial', $documentCommercial->id);

        return redirect()->route('documents-commerciaux.index')->with('message', 'Document supprimé.');
    }
}
