<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logs = AuditLog::query()
            ->with(['user', 'dossier'])
            ->when($request->query('user_id'), fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($request->query('dossier'), fn ($query, $numero) => $query
                ->whereHas('dossier', fn ($q) => $q->where('numero', $numero)))
            ->when($request->query('date'), fn ($query, $date) => $query->whereDate('created_at', $date))
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 25));

        return response()->json($logs);
    }
}
