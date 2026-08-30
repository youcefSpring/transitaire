<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AlerteStatut;
use App\Http\Controllers\Controller;
use App\Models\Alerte;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $alertes = Alerte::query()
            ->with('dossier')
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json($alertes);
    }

    public function update(Request $request, Alerte $alerte): JsonResponse
    {
        $donnees = $request->validate([
            'statut' => ['required', 'in:'.implode(',', array_map(
                fn ($cas) => $cas->value,
                AlerteStatut::cases(),
            ))],
        ]);

        $alerte->update($donnees);

        return response()->json($alerte);
    }
}
