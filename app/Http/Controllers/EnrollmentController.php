<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EnrollmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return EnrollmentResource::collection(
            Enrollment::where('user_id', $request->user()->id)->get()
        );
    }

    public function store(Request $request): EnrollmentResource
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $enrollment = Enrollment::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return new EnrollmentResource($enrollment);
    }

    public function show(Enrollment $enrollment): EnrollmentResource
    {
        return new EnrollmentResource($enrollment);
    }
}
