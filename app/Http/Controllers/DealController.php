<?php

namespace App\Http\Controllers;

use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DealController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return DealResource::collection(Deal::with(['stage', 'contact', 'owner'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'stage_id' => ['required', 'integer', 'exists:pipeline_stages,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $validated['owner_id'] ??= $request->user()->id;

        $deal = Deal::create($validated);

        return (new DealResource($deal->load(['stage', 'contact', 'owner'])))->response()->setStatusCode(201);
    }

    public function show(Deal $deal): DealResource
    {
        return new DealResource($deal->load(['stage', 'contact', 'owner']));
    }

    public function update(Request $request, Deal $deal): DealResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'stage_id' => ['sometimes', 'integer', 'exists:pipeline_stages,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $deal->update($validated);

        return new DealResource($deal->load(['stage', 'contact', 'owner']));
    }

    public function destroy(Deal $deal): JsonResponse
    {
        $deal->delete();

        return response()->json(null, 204);
    }
}
