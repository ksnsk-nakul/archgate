<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\LibraryItem;
use App\Models\PipelineStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    public function services(): Response
    {
        return Inertia::render('Public/Services');
    }

    public function about(): Response
    {
        return Inertia::render('Public/About');
    }

    public function careers(): Response
    {
        return Inertia::render('Public/Careers');
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        // Honeypot check
        if ($request->filled('website')) {
            return back();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'interest' => ['nullable', 'in:learn,buy'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $contact = Contact::query()->updateOrCreate(
            ['email' => $validated['email']],
            ['name' => $validated['name'], 'phone' => $validated['phone'] ?? null],
        );

        $stage = PipelineStage::query()->orderBy('order')->first();

        $notes = collect([$validated['interest'] ?? null, $validated['message'] ?? null])
            ->filter()
            ->implode("\n");

        Deal::query()->create([
            'title' => "Lead: {$validated['name']}",
            'contact_id' => $contact->id,
            'stage_id' => $stage?->id,
            'notes' => $notes ?: null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Message received! We\'ll be in touch soon.']);

        return back();
    }

    public function libraryPreview(): Response
    {
        $items = [];

        if (class_exists(LibraryItem::class)) {
            $items = LibraryItem::select(['id', 'title', 'type', 'cover_url'])
                ->where('is_published', true)
                ->get()
                ->toArray();
        }

        return Inertia::render('Public/LibraryPreview', [
            'items' => $items,
        ]);
    }
}
