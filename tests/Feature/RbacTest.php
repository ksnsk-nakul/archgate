<?php

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('user can be assigned a role within an organization', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->admin()->create();

    $user->roles()->attach($role->id, ['organization_id' => $org->id]);

    expect($user->hasRole('admin', $org->id))->toBeTrue();
    expect($user->hasRole('manager', $org->id))->toBeFalse();
});

test('user role is scoped to organization', function () {
    $user = User::factory()->create();
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $adminRole = Role::factory()->admin()->create();
    $memberRole = Role::factory()->member()->create();

    $user->roles()->attach($adminRole->id, ['organization_id' => $org1->id]);
    $user->roles()->attach($memberRole->id, ['organization_id' => $org2->id]);

    expect($user->hasRole('admin', $org1->id))->toBeTrue();
    expect($user->hasRole('admin', $org2->id))->toBeFalse();
    expect($user->hasRole('member', $org2->id))->toBeTrue();
});

test('permission can be assigned to role', function () {
    $role = Role::factory()->admin()->create();
    $permission = Permission::factory()->create(['slug' => 'projects.create']);

    $role->permissions()->attach($permission->id);

    expect($role->permissions()->where('slug', 'projects.create')->exists())->toBeTrue();
});

test('user has permission via role within organization', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->manager()->create();
    $permission = Permission::factory()->create(['slug' => 'tasks.view']);

    $role->permissions()->attach($permission->id);
    $user->roles()->attach($role->id, ['organization_id' => $org->id]);

    expect($user->hasPermission('tasks.view', $org->id))->toBeTrue();
    expect($user->hasPermission('tasks.delete', $org->id))->toBeFalse();
});

test('organization can be created with subdomain', function () {
    $org = Organization::factory()->create(['subdomain' => 'acme-corp']);

    expect($org->subdomain)->toBe('acme-corp');
    $this->assertModelExists($org);
});

test('organization subdomains must be unique', function () {
    Organization::factory()->create(['subdomain' => 'duplicate']);

    expect(fn () => Organization::factory()->create(['subdomain' => 'duplicate']))
        ->toThrow(QueryException::class);
});
