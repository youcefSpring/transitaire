<?php

namespace App\Http\Controllers;

use App\Enums\AlerteStatut;
use App\Models\Alerte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlerteController extends Controller
{
    public function index(Request $request): View
    {
        $alertes = Alerte::query()
            ->with('dossier')
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('alertes.index', compact('alertes'));
    }

    public function update(Request $request, Alerte $alerte): RedirectResponse
    {
        $request->validate([
            'statut' => ['required', 'in:'.implode(',', array_map(
                fn ($cas) => $cas->value,
                AlerteStatut::cases(),
            ))],
        ]);

        $alerte->update($request->validated());

        return back()->with('message', 'Alerte mise à jour.');
    }
}
