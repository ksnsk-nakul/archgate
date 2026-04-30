<?php

use App\Models\LibraryContent;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

// ---------------------------------------------------------------------------
// Library Content — CRUD
// ---------------------------------------------------------------------------

test('authenticated user can list all library content', function () {
    $user = User::factory()->create();
    LibraryContent::factory(4)->create();

    $this->actingAs($user)
        ->getJson('/api/v1/content')
        ->assertOk()
        ->assertJsonCount(4, 'data');
});

test('unauthenticated user cannot list content', function () {
    $this->getJson('/api/v1/content')->assertUnauthorized();
});

test('user can create library content', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/content', [
            'title' => 'Laravel Deep Dive',
            'type' => 'video',
            'description' => 'A comprehensive video course.',
            'access_level' => 'premium',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Laravel Deep Dive')
        ->assertJsonPath('data.type', 'video')
        ->assertJsonPath('data.access_level', 'premium');

    expect(LibraryContent::where('title', 'Laravel Deep Dive')->exists())->toBeTrue();
});

test('content creation requires title and type', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/content', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'type']);
});

test('content type must be a valid value', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/content', [
            'title' => 'Bad Type',
            'type' => 'podcast',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

test('content access_level must be valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/content', [
            'title' => 'Test',
            'type' => 'article',
            'access_level' => 'enterprise',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['access_level']);
});

test('user can update library content', function () {
    $user = User::factory()->create();
    $content = LibraryContent::factory()->create(['access_level' => 'free']);

    $this->actingAs($user)
        ->putJson("/api/v1/content/{$content->id}", ['access_level' => 'basic'])
        ->assertOk()
        ->assertJsonPath('data.access_level', 'basic');
});

test('user can delete library content', function () {
    $user = User::factory()->create();
    $content = LibraryContent::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/content/{$content->id}")
        ->assertNoContent();

    expect(LibraryContent::find($content->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Subscription middleware — content show gating
// ---------------------------------------------------------------------------

test('user with active premium subscription can view any content', function () {
    $plan = SubscriptionPlan::factory()->premium()->create();
    $user = User::factory()->create();
    UserSubscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
    ]);

    $content = LibraryContent::factory()->create(['access_level' => 'premium']);

    $this->actingAs($user)
        ->getJson("/api/v1/content/{$content->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $content->id);
});

test('user with basic subscription cannot view premium-gated content', function () {
    $plan = SubscriptionPlan::factory()->create(['tier' => 'basic']);
    $user = User::factory()->create();
    UserSubscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
    ]);

    $content = LibraryContent::factory()->create(['access_level' => 'premium']);

    $this->actingAs($user)
        ->getJson("/api/v1/content/{$content->id}")
        ->assertForbidden()
        ->assertJsonPath('message', 'Subscription upgrade required.');
});

test('user with no subscription cannot view content', function () {
    $user = User::factory()->create();
    $content = LibraryContent::factory()->create(['access_level' => 'free']);

    $this->actingAs($user)
        ->getJson("/api/v1/content/{$content->id}")
        ->assertForbidden();
});

test('user with cancelled subscription cannot view content', function () {
    $plan = SubscriptionPlan::factory()->premium()->create();
    $user = User::factory()->create();
    UserSubscription::factory()->cancelled()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
    ]);

    $content = LibraryContent::factory()->create(['access_level' => 'premium']);

    $this->actingAs($user)
        ->getJson("/api/v1/content/{$content->id}")
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Subscriptions — user subscription CRUD
// ---------------------------------------------------------------------------

test('user can list their own subscriptions', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    UserSubscription::factory(2)->create(['user_id' => $user->id]);
    UserSubscription::factory(3)->create(['user_id' => $other->id]);

    $this->actingAs($user)
        ->getJson('/api/v1/subscriptions')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

test('unauthenticated user cannot list subscriptions', function () {
    $this->getJson('/api/v1/subscriptions')->assertUnauthorized();
});

test('user can subscribe to a plan', function () {
    $user = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create(['tier' => 'basic']);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/subscriptions', [
            'plan_id' => $plan->id,
            'expires_at' => now()->addYear()->toDateString(),
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.plan_id', $plan->id)
        ->assertJsonPath('data.status', 'active')
        ->assertJsonPath('data.plan.tier', 'basic');

    expect(UserSubscription::where('user_id', $user->id)->where('plan_id', $plan->id)->exists())->toBeTrue();
});

test('subscription creation requires plan_id', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/subscriptions', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['plan_id']);
});

test('subscription plan_id must exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/subscriptions', ['plan_id' => 9999])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['plan_id']);
});

test('user can view their subscription with plan details', function () {
    $user = User::factory()->create();
    $subscription = UserSubscription::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->getJson("/api/v1/subscriptions/{$subscription->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $subscription->id)
        ->assertJsonStructure(['data' => ['plan']]);
});

test('user can cancel their subscription', function () {
    $user = User::factory()->create();
    $subscription = UserSubscription::factory()->create(['user_id' => $user->id, 'status' => 'active']);

    $this->actingAs($user)
        ->putJson("/api/v1/subscriptions/{$subscription->id}", ['status' => 'cancelled'])
        ->assertOk()
        ->assertJsonPath('data.status', 'cancelled');
});

test('subscription status must be valid', function () {
    $user = User::factory()->create();
    $subscription = UserSubscription::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->putJson("/api/v1/subscriptions/{$subscription->id}", ['status' => 'paused'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('user can delete a subscription', function () {
    $user = User::factory()->create();
    $subscription = UserSubscription::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->deleteJson("/api/v1/subscriptions/{$subscription->id}")
        ->assertNoContent();

    expect(UserSubscription::find($subscription->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Subscription plans
// ---------------------------------------------------------------------------

test('free plan factory state sets tier and price correctly', function () {
    $plan = SubscriptionPlan::factory()->free()->create();

    expect($plan->tier)->toBe('free')
        ->and((float) $plan->price)->toBe(0.0);
});

test('premium plan factory state sets tier correctly', function () {
    $plan = SubscriptionPlan::factory()->premium()->create();

    expect($plan->tier)->toBe('premium')
        ->and((float) $plan->price)->toBe(99.0);
});
