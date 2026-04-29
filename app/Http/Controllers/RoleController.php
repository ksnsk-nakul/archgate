<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RoleResource::collection(Role::with('permissions')->get());
    }

    public function store(Request $request): RoleResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['nullable', 'string'],
        ]);

        $role = Role::create($validated);

        return new RoleResource($role->load('permissions'));
    }

    public function show(Role $role): RoleResource
    {
        return new RoleResource($role->load('permissions'));
    }

    public function update(Request $request, Role $role): RoleResource
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:roles,slug,'.$role->id],
            'description' => ['nullable', 'string'],
        ]);

        $role->update($validated);

        return new RoleResource($role->load('permissions'));
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(null, 204);
    }
}
