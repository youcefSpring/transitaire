<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConteneurStoreRequest;
use App\Http\Requests\ConteneurUpdateRequest;
use App\Models\Conteneur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConteneurController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $conteneurs = Conteneur::query()
            ->with('dossier')
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('dossier'), fn ($query, $numero) => $query
                ->whereHas('dossier', fn ($q) => $q->where('numero', $numero)))
            ->orderBy('date_eta')
            ->paginate($request->integer('per_page', 15));

        return response()->json($conteneurs);
    }

    public function store(ConteneurStoreRequest $request): JsonResponse
    {
        return response()->json(Conteneur::create($request->validated()), 201);
    }

    public function show(Conteneur $conteneur): JsonResponse
    {
        return response()->json($conteneur->load(['client', 'dossier']));
    }

    public function update(ConteneurUpdateRequest $request, Conteneur $conteneur): JsonResponse
    {
        $conteneur->update($request->validated());

        return response()->json($conteneur);
    }

    public function statut(ConteneurUpdateRequest $request, Conteneur $conteneur): JsonResponse
    {
        $conteneur->update($request->validated());

        return response()->json($conteneur);
    }

    public function destroy(Conteneur $conteneur): JsonResponse
    {
        $conteneur->delete();

        return response()->json(status: 204);
    }
}
