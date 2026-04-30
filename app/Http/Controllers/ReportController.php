<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PipelineStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * GET /api/v1/reports/leads
     *
     * Returns deal counts and total values grouped by pipeline stage.
     * Supports optional filters: owner_id, from_date, to_date.
     */
    public function leads(Request $request): JsonResponse
    {
        $request->validate([
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $stages = PipelineStage::orderBy('order')
            ->with(['deals' => function ($query) use ($request) {
                if ($request->filled('owner_id')) {
                    $query->where('owner_id', $request->integer('owner_id'));
                }

                if ($request->filled('from_date')) {
                    $query->whereDate('created_at', '>=', $request->input('from_date'));
                }

                if ($request->filled('to_date')) {
                    $query->whereDate('created_at', '<=', $request->input('to_date'));
                }
            }])
            ->get()
            ->map(fn (PipelineStage $stage) => [
                'stage_id' => $stage->id,
                'stage_name' => $stage->name,
                'deal_count' => $stage->deals->count(),
                'total_value' => round((float) $stage->deals->sum('value'), 2),
            ]);

        return response()->json(['data' => $stages]);
    }
}
