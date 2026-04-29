<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            Project::where('owner_id', $request->user()->id)->get()
        );
    }

    public function store(Request $request): ProjectResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,completed,archived'],
        ]);

        $project = Project::create([
            ...$validated,
            'owner_id' => $request->user()->id,
        ]);

        return (new ProjectResource($project))->response()->setStatusCode(201);
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project);
    }

    public function update(Request $request, Project $project): ProjectResource
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:active,completed,archived'],
        ]);

        $project->update($validated);

        return new ProjectResource($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(null, 204);
    }
}
