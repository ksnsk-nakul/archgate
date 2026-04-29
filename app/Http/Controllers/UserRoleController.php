<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $role = Role::findOrFail($validated['role_id']);

        $user->roles()->syncWithoutDetaching([
            $role->id => ['organization_id' => $validated['organization_id']],
        ]);

        $user->clearRolesCache();

        return response()->json(['message' => 'Role assigned successfully.']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $user->roles()
            ->wherePivot('role_id', $validated['role_id'])
            ->wherePivot('organization_id', $validated['organization_id'])
            ->detach($validated['role_id']);

        $user->clearRolesCache();

        return response()->json(null, 204);
    }
}
