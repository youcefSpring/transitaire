<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\FraisSens;
use App\Http\Controllers\Controller;
use App\Http\Requests\FournisseurStoreRequest;
use App\Http\Requests\FournisseurUpdateRequest;
use App\Models\Fournisseur;
use Illuminate\Http\JsonResponse;

class FournisseurController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Fournisseur::query()->orderBy('nom')->get());
    }

    public function store(FournisseurStoreRequest $request): JsonResponse
    {
        return response()->json(Fournisseur::create($request->validated()), 201);
    }

    public function show(Fournisseur $fournisseur): JsonResponse
    {
        return response()->json($fournisseur);
    }

    public function update(FournisseurUpdateRequest $request, Fournisseur $fournisseur): JsonResponse
    {
        $fournisseur->update($request->validated());

        return response()->json($fournisseur);
    }

    public function destroy(Fournisseur $fournisseur): JsonResponse
    {
        $fournisseur->delete();

        return response()->json(status: 204);
    }

    public function operations(Fournisseur $fournisseur): JsonResponse
    {
        return response()->json(
            $fournisseur->frais()
                ->where('sens', FraisSens::SupporteTransitaire->value)
                ->with('dossier')
                ->orderByDesc('date_frais')
                ->get()
        );
    }

    public function paiements(Fournisseur $fournisseur): JsonResponse
    {
        return response()->json([
            'message' => 'Paiements fournisseurs non modélisés (§10) — voir GAPS-TODO G-36.',
            'depenses' => $fournisseur->frais()
                ->where('sens', FraisSens::SupporteTransitaire->value)
                ->orderByDesc('date_frais')
                ->get(),
        ]);
    }
}
