<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type NavLink = { label: string; href: string };
type AppDetails = { name: string; logoUrl: string | null };
type LandingData = { nav_links: string; footer_text: string };

const page = usePage<{ appDetails: AppDetails; landing: LandingData; auth: { user: unknown } }>();

const appName  = computed(() => page.props.appDetails?.name ?? 'FluxHaven');
const isAuthed = computed(() => !!page.props.auth?.user);

const navLinks = computed<NavLink[]>(() => {
    try { return page.props.landing?.nav_links ? JSON.parse(page.props.landing.nav_links) : []; } catch { return []; }
});

const footerText = computed(() => page.props.landing?.footer_text ?? `© ${new Date().getFullYear()} FluxHaven. All rights reserved.`);

function isActive(href: string): boolean {
    if (typeof window === 'undefined') { return false; }
    return href === '/' ? window.location.pathname === '/' : window.location.pathname.startsWith(href);
}
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#051424] text-[#d4e4fa]" style="font-family: Inter, sans-serif;">
        <!-- Top Nav -->
        <header class="sticky top-0 z-50 border-b border-slate-800 bg-[#051424]/95 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-6">
                <Link href="/" class="text-lg font-extrabold text-white hover:text-[var(--primary)] transition-colors" style="font-family: Manrope, sans-serif;">
                    {{ appName }}
                </Link>
                <nav class="hidden md:flex items-center gap-1">
                    <a
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                        :class="isActive(link.href) ? 'text-[var(--primary)] bg-[var(--primary)]/10' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60'"
                    >{{ link.label }}</a>
                </nav>
                <div class="flex items-center gap-2">
                    <template v-if="isAuthed">
                        <Link href="/dashboard" class="text-sm text-slate-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-colors">Dashboard</Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="text-sm text-slate-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-colors">Sign in</Link>
                        <Link href="/register" class="text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-hover)] px-4 py-1.5 rounded-lg transition-colors">Get started</Link>
                    </template>
                </div>
            </div>
        </header>
        <main class="flex-1"><slot /></main>
        <footer class="border-t border-slate-800 bg-[#0a1929]">
            <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <span class="text-sm text-slate-500">{{ footerText }}</span>
                <nav class="flex items-center gap-4">
                    <a v-for="link in navLinks.slice(0,5)" :key="link.href" :href="link.href" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">{{ link.label }}</a>
                </nav>
            </div>
        </footer>
    </div>
</template>
