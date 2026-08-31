<?php

namespace App\Http\Controllers;

use App\Enums\UserProfile;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")))
            ->when($request->query('profile'), fn ($query, $profil) => $query->where('profile', $profil))
            ->when($request->filled('is_active'), fn ($query) => $query
                ->where('is_active', $request->boolean('is_active')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'profils' => UserProfile::cases(),
        ]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        $this->audit->journaliser($request->user(), "Création de l'utilisateur {$user->name} ({$user->email})", null, 'user', $user->id);

        return redirect()->route('users.index')->with('message', __('app.messages.utilisateur_cree'));
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $user->update($request->safe()->except(['password']));

        if ($request->filled('password')) {
            $user->update(['password' => $request->input('password')]);
        }

        $this->audit->journaliser($request->user(), "Modification de l'utilisateur {$user->name} ({$user->email})", null, 'user', $user->id);

        return redirect()->route('users.index')->with('message', __('app.messages.utilisateur_mis_a_jour'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        $this->audit->journaliser(auth()->user(), "Suppression de l'utilisateur {$user->name} ({$user->email})", null, 'user', $user->id);

        return redirect()->route('users.index')->with('message', __('app.messages.utilisateur_supprime'));
    }
}
