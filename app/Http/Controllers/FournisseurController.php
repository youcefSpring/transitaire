<?php

namespace App\Http\Controllers;

use App\Enums\FraisSens;
use App\Http\Requests\FournisseurStoreRequest;
use App\Http\Requests\FournisseurUpdateRequest;
use App\Models\Fournisseur;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FournisseurController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $fournisseurs = Fournisseur::query()
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where(fn ($q) => $q
                    ->where('nom', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%")))
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function create(): View
    {
        return view('fournisseurs.create');
    }

    public function store(FournisseurStoreRequest $request): RedirectResponse
    {
        $fournisseur = Fournisseur::create($request->validated());

        $this->audit->journaliser($request->user(), "Création du fournisseur {$fournisseur->nom}", null, 'fournisseur', $fournisseur->id);

        return redirect()->route('fournisseurs.index')->with('message', __('app.messages.fournisseur_enregistre'));
    }

    public function show(Fournisseur $fournisseur): View
    {
        $operations = $fournisseur->frais()
            ->where('sens', FraisSens::SupporteTransitaire->value)
            ->with('dossier')
            ->orderByDesc('date_frais')
            ->get();

        return view('fournisseurs.show', compact('fournisseur', 'operations'));
    }

    public function edit(Fournisseur $fournisseur): View
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(FournisseurUpdateRequest $request, Fournisseur $fournisseur): RedirectResponse
    {
        $fournisseur->update($request->validated());

        $this->audit->journaliser($request->user(), "Modification du fournisseur {$fournisseur->nom}", null, 'fournisseur', $fournisseur->id);

        return redirect()->route('fournisseurs.show', $fournisseur)->with('message', __('app.messages.fournisseur_mis_a_jour'));
    }

    public function destroy(Fournisseur $fournisseur): RedirectResponse
    {
        $fournisseur->delete();

        $this->audit->journaliser(auth()->user(), "Suppression (logique) du fournisseur {$fournisseur->nom}", null, 'fournisseur', $fournisseur->id);

        return redirect()->route('fournisseurs.index')->with('message', __('app.messages.fournisseur_supprime'));
    }
}
