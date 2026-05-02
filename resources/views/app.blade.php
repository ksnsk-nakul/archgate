<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        @php
            $appSettings = \Illuminate\Support\Facades\Schema::hasTable('settings')
                ? app(\App\Services\SettingService::class)->appSettings()
                : ['app_name' => config('app.name', 'Laravel'), 'favicon_url' => null];
        @endphp

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script nonce="{{ Vite::cspNonce() }}">
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Stitch Vibrant Professionalism theme tokens + brand primary --}}
        <style nonce="{{ Vite::cspNonce() }}">
            :root {
                /* ── Brand primary (per-tenant from DB) ─────────────────── */
                --primary:        {{ $appSettings['primary_color'] ?? '#EC5800' }};
                --primary-hover:  color-mix(in srgb, var(--primary) 85%, black);
                --primary-dim:    color-mix(in srgb, var(--primary) 12%, transparent);
                --primary-border: color-mix(in srgb, var(--primary) 25%, transparent);

                /* ── Light mode — Stitch white & slate surface system ────── */
                --app-bg:        #f8fafc;   /* slate-50  — page background   */
                --app-surface:   #ffffff;   /* white     — card / panel bg   */
                --app-elevated:  #f1f5f9;   /* slate-100 — input / hover bg  */
                --app-text:      #0f172a;   /* slate-900 — primary text      */
                --app-muted:     #64748b;   /* slate-500 — secondary text    */
                --app-border:    #e2e8f0;   /* slate-200 — borders & dividers */

                /* ── shadcn Sidebar tokens → match stitch sidebar ─────────  */
                --sidebar-background:           #ffffff;
                --sidebar-foreground:           #475569;
                --sidebar-border:               #e2e8f0;
                --sidebar-accent:               #fff7ed;
                --sidebar-accent-foreground:    {{ $appSettings['primary_color'] ?? '#EC5800' }};
                --sidebar-primary:              {{ $appSettings['primary_color'] ?? '#EC5800' }};
                --sidebar-primary-foreground:   #ffffff;
            }

            html.dark {
                /* ── Dark mode — Stitch Obsidian Ink surface system ──────── */
                --app-bg:        #020617;   /* slate-950 — page background   */
                --app-surface:   #0f172a;   /* slate-900 — card / panel bg   */
                --app-elevated:  #1e293b;   /* slate-800 — input / hover bg  */
                --app-text:      #f1f5f9;   /* slate-100 — primary text      */
                --app-muted:     #94a3b8;   /* slate-400 — secondary text    */
                --app-border:    #1e293b;   /* slate-800 — borders & dividers */

                /* ── shadcn Sidebar tokens → dark stitch sidebar ──────────  */
                --sidebar-background:           #020617;
                --sidebar-foreground:           #94a3b8;
                --sidebar-border:               #1e293b;
                --sidebar-accent:               rgba(236,88,0,0.15);
                --sidebar-accent-foreground:    {{ $appSettings['primary_color'] ?? '#EC5800' }};
                --sidebar-primary:              {{ $appSettings['primary_color'] ?? '#EC5800' }};
                --sidebar-primary-foreground:   #ffffff;
            }

            html { background-color: var(--app-bg); }
        </style>

        <link rel="icon" href="{{ $appSettings['favicon_url'] ?? '/favicon.ico' }}" sizes="any">
        @unless ($appSettings['favicon_url'] ?? null)
            <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        @endunless
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|inter:400,500,600,700|manrope:600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        <x-inertia::head>
            <title>{{ $appSettings['app_name'] }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
