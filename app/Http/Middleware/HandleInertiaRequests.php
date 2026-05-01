<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if (! Schema::hasTable('settings')) {
            $appSettings = ['app_name' => config('app.name'), 'logo_url' => null, 'favicon_url' => null];
            $landingSettings = $this->defaultLandingSettings();
        } else {
            $service = app(SettingService::class);

            $appSettings = Cache::remember('settings:app', now()->addMinutes(5), fn () => $service->appSettings());

            $landingSettings = Cache::remember('settings:landing', now()->addMinutes(5), fn () => $service->landingSettings());
        }

        return [
            ...parent::share($request),
            'name' => $appSettings['app_name'],
            'appDetails' => [
                'name' => $appSettings['app_name'],
                'logoUrl' => $appSettings['logo_url'],
                'faviconUrl' => $appSettings['favicon_url'],
            ],
            'landing' => $landingSettings,
            'auth' => [
                'user' => $request->user(),
                'isAdmin' => $request->user()?->hasRole('superadmin')
                    || $request->user()?->hasRole('admin')
                    || $request->user()?->hasPermission('settings.view')
                    || $request->user()?->hasPermission('roles.view'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultLandingSettings(): array
    {
        return [
            'hero_title' => 'Build, Learn & Grow with FluxHaven',
            'hero_subtitle' => 'The all-in-one platform for teams, learners, and businesses.',
            'cta_label' => 'Get started free',
            'about_text' => 'FluxHaven is built for modern teams who want to learn and ship faster.',
            'footer_text' => '© '.date('Y').' FluxHaven. All rights reserved.',
            'contact_email' => '',
            'contact_phone' => '',
            'contact_address' => '',
            'nav_links' => json_encode([
                ['label' => 'Home',     'href' => '/'],
                ['label' => 'Services', 'href' => '/services'],
                ['label' => 'Library',  'href' => '/library-preview'],
                ['label' => 'About',    'href' => '/about'],
                ['label' => 'Careers',  'href' => '/careers'],
                ['label' => 'Contact',  'href' => '/contact'],
            ]),
            'services' => json_encode([
                ['icon' => 'briefcase', 'title' => 'Project Management', 'body' => 'Track projects, tasks, and deadlines in one place.'],
                ['icon' => 'users',     'title' => 'CRM & Deals',        'body' => 'Manage leads, contacts, and your sales pipeline.'],
                ['icon' => 'book',      'title' => 'Library',            'body' => 'Access books, articles, and learning resources.'],
                ['icon' => 'graduation', 'title' => 'Courses',            'body' => 'Structured learning paths with tutor support.'],
            ]),
            'careers' => json_encode([]),
        ];
    }
}
