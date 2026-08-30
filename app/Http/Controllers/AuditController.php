<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::query()
            ->with(['user', 'dossier'])
            ->when($request->query('user_id'), fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($request->query('dossier'), fn ($query, $numero) => $query
                ->whereHas('dossier', fn ($q) => $q->where('numero', $numero)))
            ->when($request->query('date'), fn ($query, $date) => $query
                ->whereDate('created_at', $date))
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('audit.index', compact('logs'));
    }
}
