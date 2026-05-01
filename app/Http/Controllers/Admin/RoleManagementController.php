<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubadminRoleRequest;
use App\Http\Requests\Admin\UpdateRolePermissionsRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class RoleManagementController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Role::class);

        $query = Role::query()
            ->withCount('users')
            ->with('permissions')
            ->orderBy('name');

        if (Schema::hasColumn('roles', 'is_system')) {
            $query->orderByDesc('is_system');
        }

        return Inertia::render('Admin/Roles/Index', [
            'roles' => RoleResource::collection($query->get()),
        ]);
    }

    public function store(StoreSubadminRoleRequest $request, RoleManagementService $roles): RedirectResponse
    {
        $role = $roles->createSubadminRole($request->validated(), $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subadmin role created.')]);

        return to_route('admin.roles.permissions.edit', $role);
    }

    public function editPermissions(Role $role): Response
    {
        Gate::authorize('updatePermissions', $role);

        return Inertia::render('Admin/Roles/Permissions', [
            'role' => new RoleResource($role->load('permissions')),
            'permissionGroups' => Permission::query()
                ->orderBy('group')
                ->orderBy('name')
                ->get()
                ->groupBy(fn (Permission $permission): string => $permission->group ?? 'General')
                ->map(fn ($permissions) => PermissionResource::collection($permissions))
                ->all(),
        ]);
    }

    public function updatePermissions(
        UpdateRolePermissionsRequest $request,
        Role $role,
        RoleManagementService $roles,
    ): RedirectResponse {
        $roles->updatePermissions($role, $request->validated('permissions', []));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role permissions updated.')]);

        return to_route('admin.roles.permissions.edit', $role);
    }
}
