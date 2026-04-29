<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class OrganizationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return OrganizationResource::collection(Organization::all());
    }

    public function store(Request $request): OrganizationResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:63', 'unique:organizations', 'alpha_dash'],
            'settings' => ['nullable', 'array'],
        ]);

        $organization = Organization::create($validated);

        return new OrganizationResource($organization);
    }

    public function show(Organization $organization): OrganizationResource
    {
        $organization = Cache::remember("organization:{$organization->id}", now()->addMinutes(60), fn () => $organization);

        return new OrganizationResource($organization);
    }

    public function update(Request $request, Organization $organization): OrganizationResource
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'subdomain' => ['sometimes', 'string', 'max:63', 'unique:organizations,subdomain,'.$organization->id, 'alpha_dash'],
            'settings' => ['nullable', 'array'],
        ]);

        $organization->update($validated);
        Cache::forget("organization:{$organization->id}");

        return new OrganizationResource($organization);
    }

    public function destroy(Organization $organization): JsonResponse
    {
        Cache::forget("organization:{$organization->id}");
        $organization->delete();

        return response()->json(null, 204);
    }
}
