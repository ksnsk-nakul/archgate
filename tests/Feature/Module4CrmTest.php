<?php

use App\Models\Contact;
use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

// ---------------------------------------------------------------------------
// Contacts
// ---------------------------------------------------------------------------

test('authenticated user can list contacts', function () {
    $user = User::factory()->create();
    Contact::factory(3)->create();

    $this->actingAs($user)
        ->getJson('/api/v1/contacts')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot list contacts', function () {
    $this->getJson('/api/v1/contacts')->assertUnauthorized();
});

test('user can create a contact', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/contacts', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-1234',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Jane Doe')
        ->assertJsonPath('data.email', 'jane@example.com');

    expect(Contact::where('name', 'Jane Doe')->exists())->toBeTrue();
});

test('contact creation requires name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/contacts', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('contact email must be a valid email', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/contacts', [
            'name' => 'Bad Email',
            'email' => 'not-an-email',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('user can view a contact', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $this->actingAs($user)
        ->getJson("/api/v1/contacts/{$contact->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $contact->id);
});

test('user can update a contact', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/v1/contacts/{$contact->id}", ['name' => 'Updated Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name');
});

test('user can delete a contact', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $this->actingAs($user)
        ->deleteJson("/api/v1/contacts/{$contact->id}")
        ->assertNoContent();

    expect(Contact::find($contact->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Pipeline Stages
// ---------------------------------------------------------------------------

test('user can list pipeline stages', function () {
    $user = User::factory()->create();
    PipelineStage::factory(3)->create();

    $this->actingAs($user)
        ->getJson('/api/v1/stages')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('user can create a pipeline stage', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/stages', [
            'name' => 'Qualified',
            'order' => 1,
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Qualified')
        ->assertJsonPath('data.order', 1);

    expect(PipelineStage::where('name', 'Qualified')->exists())->toBeTrue();
});

test('pipeline stage creation requires name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/stages', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

// ---------------------------------------------------------------------------
// Deals
// ---------------------------------------------------------------------------

test('authenticated user can list deals', function () {
    $user = User::factory()->create();
    Deal::factory(3)->create(['owner_id' => $user->id]);

    $this->actingAs($user)
        ->getJson('/api/v1/deals')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot list deals', function () {
    $this->getJson('/api/v1/deals')->assertUnauthorized();
});

test('user can create a deal', function () {
    $user = User::factory()->create();
    $stage = PipelineStage::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/deals', [
            'title' => 'Big Enterprise Deal',
            'value' => 50000.00,
            'stage_id' => $stage->id,
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Big Enterprise Deal')
        ->assertJsonPath('data.stage_id', $stage->id)
        ->assertJsonPath('data.owner_id', $user->id);

    expect(Deal::where('title', 'Big Enterprise Deal')->exists())->toBeTrue();
});

test('deal creation requires title and stage_id', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/deals', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'stage_id']);
});

test('deal stage_id must exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/v1/deals', [
            'title' => 'Deal',
            'stage_id' => 9999,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['stage_id']);
});

test('user can view a deal with stage and contact', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();
    $deal = Deal::factory()->create([
        'owner_id' => $user->id,
        'contact_id' => $contact->id,
    ]);

    $this->actingAs($user)
        ->getJson("/api/v1/deals/{$deal->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $deal->id)
        ->assertJsonPath('data.contact_id', $contact->id);
});

test('user can update a deal', function () {
    $user = User::factory()->create();
    $deal = Deal::factory()->create(['owner_id' => $user->id]);
    $newStage = PipelineStage::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/v1/deals/{$deal->id}", [
            'title' => 'Renamed Deal',
            'stage_id' => $newStage->id,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Renamed Deal')
        ->assertJsonPath('data.stage_id', $newStage->id);
});

test('user can delete a deal', function () {
    $user = User::factory()->create();
    $deal = Deal::factory()->create(['owner_id' => $user->id]);

    $this->actingAs($user)
        ->deleteJson("/api/v1/deals/{$deal->id}")
        ->assertNoContent();

    expect(Deal::find($deal->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// CRM Lead Report
// ---------------------------------------------------------------------------

test('leads report returns deals grouped by stage', function () {
    $user = User::factory()->create();
    $stage1 = PipelineStage::factory()->create(['name' => 'Lead', 'order' => 0]);
    $stage2 = PipelineStage::factory()->create(['name' => 'Closed', 'order' => 1]);

    Deal::factory(2)->create(['stage_id' => $stage1->id, 'owner_id' => $user->id, 'value' => 1000]);
    Deal::factory(1)->create(['stage_id' => $stage2->id, 'owner_id' => $user->id, 'value' => 5000]);

    $response = $this->actingAs($user)
        ->getJson('/api/v1/reports/leads')
        ->assertOk();

    $data = $response->json('data');

    $leadStage = collect($data)->firstWhere('stage_id', $stage1->id);
    $closedStage = collect($data)->firstWhere('stage_id', $stage2->id);

    expect($leadStage['deal_count'])->toBe(2);
    expect((float) $leadStage['total_value'])->toEqual(2000.0);
    expect($closedStage['deal_count'])->toBe(1);
    expect((float) $closedStage['total_value'])->toEqual(5000.0);
});

test('leads report can be filtered by owner', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $stage = PipelineStage::factory()->create();

    Deal::factory(2)->create(['stage_id' => $stage->id, 'owner_id' => $user->id]);
    Deal::factory(3)->create(['stage_id' => $stage->id, 'owner_id' => $other->id]);

    $response = $this->actingAs($user)
        ->getJson("/api/v1/reports/leads?owner_id={$user->id}")
        ->assertOk();

    $stageData = collect($response->json('data'))->firstWhere('stage_id', $stage->id);

    expect($stageData['deal_count'])->toBe(2);
});

test('unauthenticated user cannot access leads report', function () {
    $this->getJson('/api/v1/reports/leads')->assertUnauthorized();
});
