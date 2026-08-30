<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\RapportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RapportController extends Controller
{
    public function __construct(
        private readonly RapportService $rapports,
    ) {}

    public function show(string $type, Request $request): JsonResponse|Response
    {
        if (! in_array($type, RapportService::TYPES, true)) {
            return response()->json(['message' => 'Type de rapport introuvable.'], 404);
        }

        $request->validate([
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date', 'after_or_equal:du'],
        ]);

        $du = $request->date('du')?->startOfDay() ?? now()->startOfYear();
        $au = $request->date('au')?->endOfDay() ?? now()->endOfDay();

        return response()->json([
            'type' => $type,
            'du' => $du->format('d/m/Y'),
            'au' => $au->format('d/m/Y'),
            'rapport' => $this->rapports->generer($type, $du, $au),
        ]);
    }
}
