<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Chauffeur::query()->orderBy('nom')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
        ]);

        return response()->json(Chauffeur::create($request->validated()), 201);
    }

    public function update(Request $request, Chauffeur $chauffeur): JsonResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
        ]);

        $chauffeur->update($request->validated());

        return response()->json($chauffeur);
    }

    public function destroy(Chauffeur $chauffeur): JsonResponse
    {
        $chauffeur->delete();

        return response()->json(status: 204);
    }
}
