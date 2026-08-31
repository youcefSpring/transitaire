<?php

namespace App\Http\Controllers;

use App\Enums\AlerteStatut;
use App\Models\Alerte;
use App\Models\Notification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlerteController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $alertes = Alerte::query()
            ->with('dossier')
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where('message', 'like', "%{$search}%"))
            ->when($request->query('statut'), fn ($query, $statut) => $query->where('statut', $statut))
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('alertes.index', [
            'alertes' => $alertes,
            'notifications' => Notification::query()->latest('id')->limit(10)->get(),
        ]);
    }

    public function update(Request $request, Alerte $alerte): RedirectResponse
    {
        $donnees = $request->validate([
            'statut' => ['required', 'in:'.implode(',', array_map(
                fn ($cas) => $cas->value,
                AlerteStatut::cases(),
            ))],
        ]);

        $alerte->update($donnees);

        $this->audit->journaliser(
            $request->user(),
            "Alerte « {$alerte->type->value} » passée au statut {$alerte->statut->value}",
            $alerte->dossier,
            'alerte',
            $alerte->id,
        );

        return back()->with('message', __('app.messages.alerte_mise_a_jour'));
    }
}
