<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::orderBy('name')->paginate(15)]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('users.index')->with('message', 'Utilisateur créé.');
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

        return redirect()->route('users.index')->with('message', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')->with('message', 'Utilisateur supprimé.');
    }
}
