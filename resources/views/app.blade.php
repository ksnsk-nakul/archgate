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

        {{-- Inline style to set the HTML background color and primary theme colour --}}
        <style nonce="{{ Vite::cspNonce() }}">
            :root {
                /* ── Brand primary (per-tenant from DB) ─────────────────── */
                --primary:        {{ $appSettings['primary_color'] ?? '#EC5800' }};

                /* ── Light mode — Stitch white & slate surface system ────── */
                --app-bg:        #f8fafc;
                --app-surface:   #ffffff;
                --app-elevated:  #f1f5f9;
                --app-text:      #0f172a;
                --app-muted:     #64748b;
                --app-border:    #e2e8f0;
            }

            html.dark {
                /* ── Dark mode — Stitch Obsidian Ink surface system ──────── */
                --app-bg:        #020617;
                --app-surface:   #0f172a;
                --app-elevated:  #1e293b;
                --app-text:      #f1f5f9;
                --app-muted:     #94a3b8;
                --app-border:    #1e293b;
            }

            :root {
                --primary: {{ $appSettings['primary_color'] ?? '#f7600d' }};
                --primary-hover: color-mix(in srgb, var(--primary) 85%, black);
                --primary-dim: color-mix(in srgb, var(--primary) 10%, transparent);
                --primary-border: color-mix(in srgb, var(--primary) 20%, transparent);
            }
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
