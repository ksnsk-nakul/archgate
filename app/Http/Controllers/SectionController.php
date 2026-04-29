<?php

namespace App\Http\Controllers;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SectionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SectionResource::collection(Section::all());
    }

    public function store(Request $request): SectionResource
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $section = Section::create($validated);

        return new SectionResource($section);
    }

    public function show(Section $section): SectionResource
    {
        return new SectionResource($section);
    }

    public function update(Request $request, Section $section): SectionResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'course_id' => ['sometimes', 'exists:courses,id'],
        ]);

        $section->update($validated);

        return new SectionResource($section);
    }

    public function destroy(Section $section): JsonResponse
    {
        $section->delete();

        return response()->json(null, 204);
    }
}
