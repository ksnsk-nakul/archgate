<?php

namespace App\Http\Controllers;

use App\Http\Resources\PipelineStageResource;
use App\Models\PipelineStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PipelineStageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PipelineStageResource::collection(PipelineStage::orderBy('order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $stage = PipelineStage::create($validated);

        return (new PipelineStageResource($stage))->response()->setStatusCode(201);
    }
}
