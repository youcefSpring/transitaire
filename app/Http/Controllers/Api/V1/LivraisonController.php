<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\LivraisonStatut;
use App\Http\Controllers\Controller;
use App\Http\Requests\LivraisonStatutRequest;
use App\Http\Requests\LivraisonStoreRequest;
use App\Models\Livraison;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LivraisonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $livraisons = Livraison::query()
            ->with(['dossier', 'camion', 'chauffeur', 'transporteurExterne'])
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('date'), fn ($query, $date) => $query
                ->whereDate('date_heure_chargement', $date))
            ->orderBy('date_heure_chargement')
            ->paginate($request->integer('per_page', 15));

        return response()->json($livraisons);
    }

    public function store(LivraisonStoreRequest $request): JsonResponse
    {
        return response()->json(Livraison::create($request->validated()), 201);
    }

    public function show(Livraison $livraison): JsonResponse
    {
        return response()->json($livraison->load(['dossier', 'camion', 'chauffeur', 'transporteurExterne']));
    }

    public function statut(LivraisonStatutRequest $request, Livraison $livraison): JsonResponse
    {
        $livraison->update(['statut' => $request->enum('statut', LivraisonStatut::class)]);

        return response()->json($livraison);
    }

    public function destroy(Livraison $livraison): JsonResponse
    {
        $livraison->delete();

        return response()->json(status: 204);
    }
}
