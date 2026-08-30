<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCommercialStatut;
use App\Enums\DocumentCommercialType;
use App\Http\Requests\PaiementStoreRequest;
use App\Models\Client;
use App\Models\DocumentCommercial;
use App\Models\Paiement;
use App\Services\AuditService;
use App\Services\SoldeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementController extends Controller
{
    public function __construct(
        private readonly SoldeService $solde,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $paiements = Paiement::query()
            ->with('client')
            ->when($request->query('mode'), fn ($query, $mode) => $query->where('mode', $mode))
            ->when($request->query('du'), fn ($query, $du) => $query->whereDate('date_paiement', '>=', $du))
            ->when($request->query('au'), fn ($query, $au) => $query->whereDate('date_paiement', '<=', $au))
            ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->orderByDesc('date_paiement')
            ->paginate(15)
            ->withQueryString();

        return view('paiements.index', [
            'paiements' => $paiements,
            'clients' => Client::orderBy('raison_sociale')->get(),
            'factures' => DocumentCommercial::query()
                ->where('type', DocumentCommercialType::Facture->value)
                ->whereNot('statut', DocumentCommercialStatut::Annule->value)
                ->orderByDesc('numero')
                ->get(['id', 'numero', 'montant_total', 'devise']),
        ]);
    }

    public function store(PaiementStoreRequest $request): RedirectResponse
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

        return back()->with('message', 'Paiement enregistré.');
    }
}
