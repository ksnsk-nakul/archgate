<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $tier = 'basic'): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $tiers = ['free', 'basic', 'premium'];
        $requiredIndex = array_search($tier, $tiers);

        $subscription = $user->subscriptions()
            ->with('plan')
            ->where('status', 'active')
            ->latest()
            ->first();

        $userTierIndex = $subscription
            ? array_search($subscription->plan->tier, $tiers)
            : 0;

        if ($userTierIndex === false || $userTierIndex < $requiredIndex) {
            return response()->json(['message' => 'Subscription upgrade required.'], 403);
        }

        return $next($request);
    }
}
