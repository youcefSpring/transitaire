<?php

namespace App\Http\Controllers;

use App\Enums\FraisSens;
use App\Http\Requests\FournisseurStoreRequest;
use App\Http\Requests\FournisseurUpdateRequest;
use App\Models\Fournisseur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FournisseurController extends Controller
{
    public function index(Request $request): View
    {
        $fournisseurs = Fournisseur::query()
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->orderBy('nom')
            ->paginate(15);

        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function create(): View
    {
        return view('fournisseurs.create');
    }

    public function store(FournisseurStoreRequest $request): RedirectResponse
    {
        Fournisseur::create($request->validated());

        return redirect()->route('fournisseurs.index')->with('message', 'Fournisseur enregistré.');
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

        return redirect()->route('fournisseurs.show', $fournisseur)->with('message', 'Fournisseur mis à jour.');
    }

    public function destroy(Fournisseur $fournisseur): RedirectResponse
    {
        $fournisseur->delete();

        return redirect()->route('fournisseurs.index')->with('message', 'Fournisseur supprimé.');
    }
}
