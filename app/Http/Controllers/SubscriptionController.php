<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionResource;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubscriptionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return SubscriptionResource::collection(
            UserSubscription::with('plan')
                ->where('user_id', $request->user()->id)
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'status' => ['sometimes', 'string', 'in:active,cancelled,expired'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $subscription = UserSubscription::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => $validated['status'] ?? 'active',
        ]);

        return (new SubscriptionResource($subscription->load('plan')))->response()->setStatusCode(201);
    }

    public function show(UserSubscription $subscription): SubscriptionResource
    {
        return new SubscriptionResource($subscription->load('plan'));
    }

    public function update(Request $request, UserSubscription $subscription): SubscriptionResource
    {
        $validated = $request->validate([
            'plan_id' => ['sometimes', 'exists:subscription_plans,id'],
            'status' => ['sometimes', 'string', 'in:active,cancelled,expired'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $subscription->update($validated);

        return new SubscriptionResource($subscription->load('plan'));
    }

    public function destroy(UserSubscription $subscription): JsonResponse
    {
        $subscription->delete();

        return response()->json(null, 204);
    }
}
