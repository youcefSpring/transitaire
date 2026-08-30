<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Camion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CamionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Camion::query()->orderBy('immatriculation')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'immatriculation' => ['required', 'string', 'max:50', 'unique:camions,immatriculation'],
            'notes' => ['nullable', 'string'],
        ]);

        return response()->json(Camion::create($request->validated()), 201);
    }

    public function update(Request $request, Camion $camion): JsonResponse
    {
        $request->validate([
            'immatriculation' => ['required', 'string', 'max:50', 'unique:camions,immatriculation,'.$camion->id],
            'notes' => ['nullable', 'string'],
        ]);

        $camion->update($request->validated());

        return response()->json($camion);
    }

    public function destroy(Camion $camion): JsonResponse
    {
        $camion->delete();

        return response()->json(status: 204);
    }
}
