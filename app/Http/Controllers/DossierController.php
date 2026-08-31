<?php

namespace App\Http\Controllers;

use App\Enums\DossierStatut;
use App\Http\Requests\DossierBlocageRequest;
use App\Http\Requests\DossierStatutRequest;
use App\Http\Requests\DossierStoreRequest;
use App\Http\Requests\DossierUpdateRequest;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Fournisseur;
use App\Services\AuditService;
use App\Services\DossierService;
use App\Services\MargeService;
use App\Services\NumerotationService;
use App\Services\PdfExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DossierController extends Controller
{
    public function __construct(
        private readonly NumerotationService $numeros,
        private readonly DossierService $dossiers,
        private readonly MargeService $marges,
        private readonly AuditService $audit,
        private readonly PdfExportService $pdfs,
    ) {}

    public function index(Request $request): View
    {
        $dossiers = Dossier::query()
            ->with('client')
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where(fn ($q) => $q
                    ->where('numero', 'like', "%{$search}%")
                    ->orWhere('numero_bl_awb', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('raison_sociale', 'like', "%{$search}%"))))
            ->when($request->query('client_id'), fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->when($request->query('bloque'), fn ($query) => $query->where('bloque', true))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('dossiers.index', [
            'dossiers' => $dossiers,
            'clients' => Client::orderBy('raison_sociale')->get(['id', 'raison_sociale']),
        ]);
    }

    public function create(): View
    {
        return view('dossiers.create', ['clients' => Client::orderBy('raison_sociale')->get()]);
    }

    public function store(DossierStoreRequest $request): RedirectResponse
    {
        $dossier = Dossier::create($request->validated() + [
            'numero' => $this->numeros->prochainNumeroDossier(),
            'created_by' => $request->user()->id,
        ]);

        $this->audit->journaliser($request->user(), "Création du dossier #{$dossier->numero}", $dossier);

        return redirect()->route('dossiers.show', $dossier->numero)->with('message', __('app.messages.dossier_cree', ['numero' => $dossier->numero]));
    }

    public function show(string $numero): View
    {
        $dossier = $this->dossier($numero)->load([
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
        ]);

        $marge = $this->marges->margeDossier($dossier);
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('dossiers.show', compact('dossier', 'marge', 'fournisseurs'));
    }

    public function edit(string $numero): View
    {
        return view('dossiers.edit', [
            'dossier' => $this->dossier($numero),
            'clients' => Client::orderBy('raison_sociale')->get(),
        ]);
    }

    /**
     * Synthèse officielle du dossier (PDF), style algérien.
     */
    public function pdf(string $numero): Response
    {
        $dossier = $this->dossier($numero)->load([
            'client',
            'marchandises',
            'douaneEtapes.executedBy',
            'frais.fournisseur',
            'documentsCommerciaux',
        ]);

        $marge = $this->marges->margeDossier($dossier);

        return $this->pdfs->telecharger('pdf.dossier', [
            'dossier' => $dossier,
            'marge' => $marge,
        ], "{$dossier->numero}.pdf");
    }

    public function update(DossierUpdateRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);
        $dossier->update($request->validated());

        $this->audit->journaliser($request->user(), "Modification du dossier #{$dossier->numero}", $dossier);

        return redirect()->route('dossiers.show', $dossier->numero)->with('message', __('app.messages.dossier_mis_a_jour'));
    }

    public function statut(DossierStatutRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossiers->changerStatut($this->dossier($numero), $request->enum('statut', DossierStatut::class), $request->user());

        $this->audit->journaliser($request->user(), "Passage du dossier #{$dossier->numero} au statut {$dossier->statut->value}", $dossier);

        return redirect()->route('dossiers.show', $dossier->numero)->with('message', __('app.messages.statut_mis_a_jour'));
    }

    public function blocage(DossierBlocageRequest $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);

        $dossier = $request->boolean('bloque')
            ? $this->dossiers->bloquer($dossier, (string) $request->input('raison'), $request->user())
            : $this->dossiers->debloquer($dossier, $request->user());

        $this->audit->journaliser(
            $request->user(),
            $request->boolean('bloque')
                ? "Blocage du dossier #{$dossier->numero} : {$dossier->raison_blocage}"
                : "Déblocage du dossier #{$dossier->numero}",
            $dossier,
        );

        return redirect()->route('dossiers.show', $dossier->numero)->with('message', $request->boolean('bloque') ? __('app.messages.dossier_bloque') : __('app.messages.dossier_debloque'));
    }

    public function destroy(Request $request, string $numero): RedirectResponse
    {
        $dossier = $this->dossier($numero);
        $dossier->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) du dossier #{$dossier->numero}", $dossier);

        return redirect()->route('dossiers.index')->with('message', __('app.messages.dossier_supprime'));
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
