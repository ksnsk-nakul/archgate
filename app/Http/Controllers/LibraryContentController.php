<?php

namespace App\Http\Controllers;

use App\Http\Resources\LibraryContentResource;
use App\Models\LibraryContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LibraryContentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return LibraryContentResource::collection(LibraryContent::all());
    }

    public function store(Request $request): LibraryContentResource
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:article,video,pdf,audio'],
            'description' => ['nullable', 'string'],
            'access_level' => ['sometimes', 'string', 'in:free,basic,premium'],
        ]);

        $content = LibraryContent::create($validated);

        return new LibraryContentResource($content);
    }

    public function show(LibraryContent $content): LibraryContentResource
    {
        return new LibraryContentResource($content);
    }

    public function update(Request $request, LibraryContent $content): LibraryContentResource
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:article,video,pdf,audio'],
            'description' => ['nullable', 'string'],
            'access_level' => ['sometimes', 'string', 'in:free,basic,premium'],
        ]);

        $content->update($validated);

        return new LibraryContentResource($content);
    }

    public function destroy(LibraryContent $content): JsonResponse
    {
        $content->delete();

        return response()->json(null, 204);
    }
}
