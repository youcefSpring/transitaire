<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(User::orderBy('name')->get());
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        return response()->json(User::create($request->validated()), 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $user->update($request->safe()->except(['password']));

        if ($request->filled('password')) {
            $user->update(['password' => $request->input('password')]);
        }

        return response()->json($user->fresh());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $user->delete();

        $this->audit->journaliser($request->user(), "Suppression (logique) de l'utilisateur {$user->name}", null, 'user', $user->id);

        return response()->json(status: 204);
    }
}
