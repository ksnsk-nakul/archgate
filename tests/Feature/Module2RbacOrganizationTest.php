<?php

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(LazilyRefreshDatabase::class);

// ---------------------------------------------------------------------------
// Organization CRUD
// ---------------------------------------------------------------------------

test('authenticated user can create an organization', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/organizations', [
            'name' => 'Acme Corp',
            'subdomain' => 'acme',
        ])
        ->assertCreated()
        ->assertJsonPath('data.subdomain', 'acme');
});

test('organization subdomain must be unique', function () {
    $user = User::factory()->create();
    Organization::factory()->create(['subdomain' => 'taken']);

    $this->actingAs($user)
        ->postJson('/api/v1/organizations', [
            'name' => 'Another',
            'subdomain' => 'taken',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['subdomain']);
});

test('authenticated user can retrieve an organization', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();

    $this->actingAs($user)
        ->getJson("/api/v1/organizations/{$org->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $org->id);
});

test('authenticated user can update an organization', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/v1/organizations/{$org->id}", ['name' => 'Updated Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name');
});

test('authenticated user can delete an organization', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/organizations/{$org->id}")
        ->assertNoContent();

    $this->assertModelMissing($org);
});

test('unauthenticated request to organizations is rejected', function () {
    $org = Organization::factory()->create();

    $this->getJson("/api/v1/organizations/{$org->id}")->assertUnauthorized();
});

// ---------------------------------------------------------------------------
// Role CRUD
// ---------------------------------------------------------------------------

test('authenticated user can list roles', function () {
    $user = User::factory()->create();
    Role::factory()->count(3)->create();

    $this->actingAs($user)
        ->getJson('/api/v1/roles')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('authenticated user can create a role', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/roles', [
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Can edit content',
        ])
        ->assertCreated()
        ->assertJsonPath('data.slug', 'editor');
});

test('role slug must be unique', function () {
    $user = User::factory()->create();
    Role::factory()->create(['slug' => 'existing-role']);

    $this->actingAs($user)
        ->postJson('/api/v1/roles', [
            'name' => 'Duplicate',
            'slug' => 'existing-role',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);
});

test('authenticated user can update a role', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/v1/roles/{$role->id}", ['name' => 'Super Editor'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Super Editor');
});

test('authenticated user can delete a role', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/roles/{$role->id}")
        ->assertNoContent();

    $this->assertModelMissing($role);
});

// ---------------------------------------------------------------------------
// User Role Assignments
// ---------------------------------------------------------------------------

test('can assign a role to a user within an organization', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->admin()->create();

    $this->actingAs($actor)
        ->postJson('/api/v1/user-role-assignments', [
            'user_id' => $target->id,
            'role_id' => $role->id,
            'organization_id' => $org->id,
        ])
        ->assertOk()
        ->assertJsonPath('message', 'Role assigned successfully.');

    expect($target->fresh()->hasRole('admin', $org->id))->toBeTrue();
});

test('role assignment clears the user roles cache', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->admin()->create();

    Cache::put("user:roles:{$target->id}", collect(), now()->addMinutes(15));

    $this->actingAs($actor)
        ->postJson('/api/v1/user-role-assignments', [
            'user_id' => $target->id,
            'role_id' => $role->id,
            'organization_id' => $org->id,
        ])
        ->assertOk();

    expect(Cache::has("user:roles:{$target->id}"))->toBeFalse();
});

test('can remove a role from a user', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->admin()->create();

    $target->roles()->attach($role->id, ['organization_id' => $org->id]);

    $this->actingAs($actor)
        ->deleteJson('/api/v1/user-role-assignments', [
            'user_id' => $target->id,
            'role_id' => $role->id,
            'organization_id' => $org->id,
        ])
        ->assertNoContent();

    expect($target->fresh()->hasRole('admin', $org->id))->toBeFalse();
});

// ---------------------------------------------------------------------------
// User CRUD
// ---------------------------------------------------------------------------

test('authenticated user can list users', function () {
    $user = User::factory()->create();
    User::factory()->count(2)->create();

    $this->actingAs($user)
        ->getJson('/api/v1/users')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'email']]]);
});

test('authenticated user can create a user', function () {
    $actor = User::factory()->create();

    $this->actingAs($actor)
        ->postJson('/api/v1/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
        ])
        ->assertCreated()
        ->assertJsonPath('data.email', 'new@example.com');
});

test('user creation rejects duplicate email', function () {
    $actor = User::factory()->create();
    User::factory()->create(['email' => 'existing@example.com']);

    $this->actingAs($actor)
        ->postJson('/api/v1/users', [
            'name' => 'Duplicate',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('authenticated user can retrieve another user', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor)
        ->getJson("/api/v1/users/{$target->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $target->id);
});

test('authenticated user can update a user', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor)
        ->putJson("/api/v1/users/{$target->id}", ['name' => 'Updated Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name');
});

test('authenticated user can delete a user', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor)
        ->deleteJson("/api/v1/users/{$target->id}")
        ->assertNoContent();

    $this->assertModelMissing($target);
});

// ---------------------------------------------------------------------------
// RBAC Caching
// ---------------------------------------------------------------------------

test('user roles are cached after first access', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->admin()->create();
    $user->roles()->attach($role->id, ['organization_id' => $org->id]);

    Cache::forget("user:roles:{$user->id}");
    $user->cachedRoles();

    expect(Cache::has("user:roles:{$user->id}"))->toBeTrue();
});

test('clearRolesCache removes the cached entry', function () {
    $user = User::factory()->create();
    Cache::put("user:roles:{$user->id}", collect(), now()->addMinutes(15));

    $user->clearRolesCache();

    expect(Cache::has("user:roles:{$user->id}"))->toBeFalse();
});

test('hasRole uses cached roles collection', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->admin()->create();
    $user->roles()->attach($role->id, ['organization_id' => $org->id]);

    Cache::forget("user:roles:{$user->id}");

    expect($user->hasRole('admin', $org->id))->toBeTrue();
    expect(Cache::has("user:roles:{$user->id}"))->toBeTrue();
});

test('user with permission via role passes permission check', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->manager()->create();
    $permission = Permission::factory()->create(['slug' => 'tasks.view']);

    $role->permissions()->attach($permission->id);
    $user->roles()->attach($role->id, ['organization_id' => $org->id]);

    expect($user->hasPermission('tasks.view', $org->id))->toBeTrue();
    expect($user->hasPermission('tasks.delete', $org->id))->toBeFalse();
});
