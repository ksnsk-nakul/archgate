<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAppSettingsRequest;
use App\Http\Requests\Admin\UpdateThirdPartySettingsRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function app(SettingService $settings): Response
    {
        Gate::authorize('viewAny', Setting::class);

        return Inertia::render('Admin/Settings/App', [
            'settings' => $settings->appSettings(),
        ]);
    }

    public function updateApp(UpdateAppSettingsRequest $request, SettingService $settings): RedirectResponse
    {
        $settings->updateAppSettings($request->validated());

        Cache::forget('settings:app');

        Inertia::flash('toast', ['type' => 'success', 'message' => __('App settings updated.')]);

        return to_route('admin.settings.app');
    }

    public function thirdParty(SettingService $settings): Response
    {
        Gate::authorize('viewAny', Setting::class);

        return Inertia::render('Admin/Settings/ThirdParty', [
            'settings' => $settings->thirdPartySettings(),
        ]);
    }

    public function updateThirdParty(UpdateThirdPartySettingsRequest $request, SettingService $settings): RedirectResponse
    {
        $settings->updateThirdPartySettings($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Third-party settings updated.')]);

        return to_route('admin.settings.third-party');
    }

    public function landing(SettingService $settings): Response
    {
        Gate::authorize('viewAny', Setting::class);

        return Inertia::render('Admin/Settings/Landing', [
            'settings' => $settings->landingSettings(),
        ]);
    }

    public function updateLanding(Request $request, SettingService $settings): RedirectResponse
    {
        Gate::authorize('viewAny', Setting::class);

        $validated = $request->validate([
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'cta_label' => ['nullable', 'string', 'max:100'],
            'about_text' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'nav_links' => ['nullable', 'array'],
            'nav_links.*.label' => ['required', 'string', 'max:50'],
            'nav_links.*.href' => ['required', 'string', 'max:255'],
            'services' => ['nullable', 'array'],
            'services.*.icon' => ['required', 'string'],
            'services.*.title' => ['required', 'string', 'max:100'],
            'services.*.body' => ['required', 'string'],
            'careers' => ['nullable', 'array'],
            'careers.*.title' => ['required', 'string', 'max:200'],
            'careers.*.location' => ['nullable', 'string', 'max:100'],
            'careers.*.type' => ['nullable', 'string', 'max:50'],
            'careers.*.description' => ['nullable', 'string'],
            'page_enabled' => ['nullable', 'array'],
            'page_enabled.*' => ['boolean'],
        ]);

        $settings->updateLandingSettings($validated);

        Cache::forget('settings:landing');

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Landing settings updated.')]);

        return to_route('admin.settings.landing');
    }
}
