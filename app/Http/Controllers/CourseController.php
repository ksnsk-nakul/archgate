<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CourseResource::collection(Course::all());
    }

    public function store(Request $request): CourseResource
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:draft,published,archived'],
        ]);

        $course = Course::create([
            ...$validated,
            'instructor_id' => $request->user()->id,
        ]);

        return new CourseResource($course);
    }

    public function show(Course $course): CourseResource
    {
        return new CourseResource($course);
    }

    public function update(Request $request, Course $course): CourseResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:draft,published,archived'],
        ]);

        $course->update($validated);

        return new CourseResource($course);
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json(null, 204);
    }
}
