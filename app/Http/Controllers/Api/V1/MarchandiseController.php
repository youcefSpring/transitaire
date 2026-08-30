<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarchandiseStoreRequest;
use App\Models\Dossier;
use App\Models\Marchandise;
use Illuminate\Http\JsonResponse;

class MarchandiseController extends Controller
{
    public function index(string $numero): JsonResponse
    {
        return response()->json($this->dossier($numero)->marchandises()->get());
    }

    public function store(MarchandiseStoreRequest $request, string $numero): JsonResponse
    {
        $marchandise = $this->dossier($numero)->marchandises()->create($request->validated());

        return response()->json($marchandise, 201);
    }

    public function update(MarchandiseStoreRequest $request, Marchandise $marchandise): JsonResponse
    {
        $marchandise->update($request->validated());

        return response()->json($marchandise);
    }

    public function destroy(Marchandise $marchandise): JsonResponse
    {
        $marchandise->delete();

        return response()->json(status: 204);
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
