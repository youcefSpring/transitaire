<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DouaneEtape;
use App\Http\Controllers\Controller;
use App\Http\Requests\DouaneEtapeRequest;
use App\Models\Dossier;
use App\Services\DouaneService;
use Illuminate\Http\JsonResponse;

class DedouanementController extends Controller
{
    public function __construct(
        private readonly DouaneService $douane,
    ) {}

    public function index(string $numero): JsonResponse
    {
        return response()->json(
            $this->dossier($numero)->douaneEtapes()->with('executedBy')->orderBy('executed_at')->get()
        );
    }

    public function store(DouaneEtapeRequest $request, string $numero): JsonResponse
    {
        $dossier = $this->dossier($numero);

        $etape = $this->douane->enregistrerEtape(
            $dossier,
            $request->enum('etape', DouaneEtape::class),
            $request->user(),
        );

        return response()->json($etape->load('executedBy'), 201);
    }

    private function dossier(string $numero): Dossier
    {
        return Dossier::where('numero', $numero)->firstOrFail();
    }
}
