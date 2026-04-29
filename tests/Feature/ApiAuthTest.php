<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('user can register with valid credentials', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['user', 'token']);

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

test('register requires all fields', function () {
    $this->postJson('/api/v1/register', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('register rejects duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->postJson('/api/v1/register', [
        'name' => 'Another User',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('user can login with valid credentials', function () {
    User::factory()->create([
        'email' => 'login@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'login@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['user', 'token']);
});

test('login rejects invalid credentials', function () {
    User::factory()->create(['email' => 'wrong@example.com']);

    $this->postJson('/api/v1/login', [
        'email' => 'wrong@example.com',
        'password' => 'bad-password',
    ])->assertUnprocessable();
});

test('user can logout and token is revoked', function () {
    $user = User::factory()->create();
    $plainTextToken = $user->createToken('auth-token')->plainTextToken;

    expect($user->tokens()->count())->toBe(1);

    $this->withToken($plainTextToken)
        ->postJson('/api/v1/logout')
        ->assertOk()
        ->assertJson(['message' => 'Successfully logged out.']);

    expect($user->tokens()->count())->toBe(0);
});

test('me endpoint returns authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.email', $user->email);
});

test('me endpoint returns user with roles and organizations', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $role = Role::factory()->admin()->create();
    $user->roles()->attach($role->id, ['organization_id' => $org->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'roles', 'organizations'],
        ]);
});

test('unauthenticated requests to protected routes return 401', function () {
    $this->getJson('/api/v1/me')->assertUnauthorized();
    $this->postJson('/api/v1/logout')->assertUnauthorized();
});
