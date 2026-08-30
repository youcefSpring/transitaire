<?php

namespace App\Http\Controllers;

use App\Services\RapportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RapportController extends Controller
{
    public function __construct(
        private readonly RapportService $rapports,
    ) {}

    public function index(): View
    {
        return view('rapports.index', ['types' => RapportService::TYPES]);
    }

    public function show(Request $request, string $type): View
    {
        if (! in_array($type, RapportService::TYPES, true)) {
            abort(404, 'Type de rapport introuvable.');
        }

        $request->validate([
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date', 'after_or_equal:du'],
        ]);

        $du = $request->date('du')?->startOfDay() ?? now()->startOfYear();
        $au = $request->date('au')?->endOfDay() ?? now()->endOfDay();

        return view('rapports.show', [
            'type' => $type,
            'du' => $du,
            'au' => $au,
            'rapport' => $this->rapports->generer($type, $du, $au),
        ]);
    }
}
