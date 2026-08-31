<?php

namespace App\Http\Controllers;

use App\Enums\FournisseurType;
use App\Enums\LivraisonStatut;
use App\Http\Requests\LivraisonStatutRequest;
use App\Http\Requests\LivraisonStoreRequest;
use App\Models\Camion;
use App\Models\Chauffeur;
use App\Models\Dossier;
use App\Models\Fournisseur;
use App\Models\Livraison;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LivraisonController extends Controller
{
    public function camionsIndex(Request $request): View
    {
        $camions = Camion::query()
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where('immatriculation', 'like', "%{$search}%"))
            ->orderBy('immatriculation')
            ->paginate(15)
            ->withQueryString();

        return view('transport.camions', compact('camions'));
    }

    public function camionsStore(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'immatriculation' => ['required', 'string', 'max:50', 'unique:camions,immatriculation'],
            'notes' => ['nullable', 'string'],
        ]);

        Camion::create($donnees);

        return back()->with('message', __('app.messages.camion_enregistre'));
    }

    public function camionsDestroy(Camion $camion): RedirectResponse
    {
        $camion->delete();

        return back()->with('message', __('app.messages.camion_supprime'));
    }

    public function chauffeursIndex(Request $request): View
    {
        $chauffeurs = Chauffeur::query()
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('telephone', 'like', "%{$search}%"))
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('transport.chauffeurs', compact('chauffeurs'));
    }

    public function chauffeursStore(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
        ]);

        Chauffeur::create($donnees);

        return back()->with('message', __('app.messages.chauffeur_enregistre'));
    }

    public function chauffeursDestroy(Chauffeur $chauffeur): RedirectResponse
    {
        $chauffeur->delete();

        return back()->with('message', __('app.messages.chauffeur_supprime'));
    }

    public function index(Request $request): View
    {
        $livraisons = Livraison::query()
            ->with(['dossier', 'camion', 'chauffeur', 'transporteurExterne'])
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('date'), fn ($query, $date) => $query
                ->whereDate('date_heure_chargement', $date))
            ->orderBy('date_heure_chargement')
            ->paginate(15)
            ->withQueryString();

        return view('transport.livraisons', [
            'livraisons' => $livraisons,
            'dossiers' => Dossier::orderByDesc('created_at')->get(['id', 'numero']),
            'camions' => Camion::orderBy('immatriculation')->get(),
            'chauffeurs' => Chauffeur::orderBy('nom')->get(),
            'transporteurs' => Fournisseur::query()
                ->where('type', FournisseurType::Transporteur->value)
                ->orderBy('nom')->get(),
        ]);
    }

    public function store(LivraisonStoreRequest $request): RedirectResponse
    {
        Livraison::create($request->validated());

        return redirect()->route('livraisons.index')->with('message', __('app.messages.livraison_planifiee'));
    }

    public function statut(LivraisonStatutRequest $request, Livraison $livraison): RedirectResponse
    {
        $livraison->update(['statut' => $request->enum('statut', LivraisonStatut::class)]);

        return back()->with('message', __('app.messages.statut_livraison_mis_a_jour'));
    }

    public function destroy(Livraison $livraison): RedirectResponse
    {
        $livraison->delete();

        return redirect()->route('livraisons.index')->with('message', __('app.messages.livraison_supprimee'));
    }
}
