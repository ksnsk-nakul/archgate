<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LessonController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return LessonResource::collection(Lesson::all());
    }

    public function store(Request $request): LessonResource
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['sometimes', 'string', 'in:text,video,quiz'],
            'section_id' => ['required', 'exists:sections,id'],
            'order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $lesson = Lesson::create($validated);

        return new LessonResource($lesson);
    }

    public function show(Lesson $lesson): LessonResource
    {
        return new LessonResource($lesson);
    }

    public function update(Request $request, Lesson $lesson): LessonResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['sometimes', 'string', 'in:text,video,quiz'],
            'section_id' => ['sometimes', 'exists:sections,id'],
            'order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $lesson->update($validated);

        return new LessonResource($lesson);
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        $lesson->delete();

        return response()->json(null, 204);
    }
}
