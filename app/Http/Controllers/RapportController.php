<?php

namespace App\Http\Controllers;

use App\Services\PdfExportService;
use App\Services\RapportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class RapportController extends Controller
{
    public function __construct(
        private readonly RapportService $rapports,
        private readonly PdfExportService $pdfs,
    ) {}

    public function index(): View
    {
        return view('rapports.index', ['types' => RapportService::TYPES]);
    }

    public function show(Request $request, string $type): View
    {
        if (! in_array($type, RapportService::TYPES, true)) {
            abort(404, __('app.messages.rapport_introuvable'));
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

    /**
     * Rapport officiel (PDF), style algérien.
     */
    public function pdf(Request $request, string $type): Response
    {
        if (! in_array($type, RapportService::TYPES, true)) {
            abort(404, __('app.messages.rapport_introuvable'));
        }

        $request->validate([
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date', 'after_or_equal:du'],
        ]);

        $du = $request->date('du')?->startOfDay() ?? now()->startOfYear();
        $au = $request->date('au')?->endOfDay() ?? now()->endOfDay();

        return $this->pdfs->telecharger('pdf.rapport', [
            'type' => $type,
            'du' => $du,
            'au' => $au,
            'rapport' => $this->rapports->generer($type, $du, $au),
        ], "rapport-{$type}-{$du->toDateString()}-{$au->toDateString()}.pdf");
    }
}
