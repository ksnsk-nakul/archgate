<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import RichEditor from '@/components/RichEditor.vue';

type NavLink = { label: string; href: string };
type Service  = { icon: string; title: string; body: string; link_to?: string };
type Career   = { title: string; location?: string; type?: string; description?: string; link_to?: string };

type LandingSettings = {
    hero_title: string; hero_subtitle: string; cta_label: string;
    about_text: string; footer_text: string;
    contact_email: string; contact_phone: string; contact_address: string;
    nav_links: string; services: string; careers: string;
    page_enabled?: Record<string, boolean>;
};

const props = defineProps<{ settings: LandingSettings }>();

const parseJson = <T>(val: string | null | undefined, fallback: T): T => {
    try { return val ? JSON.parse(val) : fallback; } catch { return fallback; }
};

// ── Page list definition ──────────────────────────────────────────────────────
type PageKey = 'home' | 'services' | 'about' | 'careers' | 'contact' | 'library-preview' | 'footer-nav';
type PageDef = { key: PageKey; label: string; desc: string; href: string | null; icon: string; always?: boolean };

/** Canonical order — drives both the CMS list and public nav */
const pageList = ref<PageDef[]>([
    { key: 'home',            label: 'Home',            desc: 'Hero, services teaser, CTA',       href: '/',               icon: 'home',   always: true },
    { key: 'services',        label: 'Services',        desc: 'Feature / services grid',          href: '/services',        icon: 'grid' },
    { key: 'about',           label: 'About',           desc: 'Rich text about section',          href: '/about',           icon: 'info' },
    { key: 'careers',         label: 'Careers',         desc: 'Open job listings',                href: '/careers',         icon: 'work' },
    { key: 'contact',         label: 'Contact',         desc: 'Contact info + lead capture form', href: '/contact',         icon: 'mail' },
    { key: 'library-preview', label: 'Library Preview', desc: 'Public library item listing',      href: '/library-preview', icon: 'book' },
    { key: 'footer-nav',      label: 'Footer & Nav',    desc: 'Navigation links + footer text',   href: null,               icon: 'layout' },
]);

/** Options for section link_to dropdowns */
const linkablePages = computed(() => [
    { label: 'None', value: '' },
    ...pageList.value
        .filter((p) => p.href !== null && p.key !== 'footer-nav')
        .map((p) => ({ label: p.label, value: p.href as string })),
]);

// Load enabled state from props (DB value), fallback all true
const defaultEnabled: Record<PageKey, boolean> = {
    home: true, services: true, about: true, careers: true,
    contact: true, 'library-preview': true, 'footer-nav': true,
};
const pageEnabled = ref<Record<PageKey, boolean>>(
    Object.assign({}, defaultEnabled, props.settings.page_enabled ?? {}),
);

// ── Confirmation dialog for toggle ───────────────────────────────────────────
const confirmDialog = ref<{ key: PageKey; enabling: boolean } | null>(null);

function requestToggle(key: PageKey) {
    if (key === 'home') { return; }
    confirmDialog.value = { key, enabling: !pageEnabled.value[key] };
}

function cancelToggle() { confirmDialog.value = null; }

function confirmToggle() {
    if (!confirmDialog.value) { return; }
    const { key, enabling } = confirmDialog.value;
    pageEnabled.value[key] = enabling;
    confirmDialog.value = null;
    router.put('/admin/settings/landing', { page_enabled: pageEnabled.value }, {
        preserveScroll: true,
        preserveState: true,
    });
}

const pageList = [
    { key: 'home' as PageKey,            label: 'Home',            desc: 'Hero, services teaser, call-to-action', href: '/',                icon: 'home',   always: true },
    { key: 'services' as PageKey,        label: 'Services',        desc: 'Feature / services grid',               href: '/services',         icon: 'grid' },
    { key: 'about' as PageKey,           label: 'About',           desc: 'Rich text about section',               href: '/about',            icon: 'info' },
    { key: 'careers' as PageKey,         label: 'Careers',         desc: 'Open job listings',                     href: '/careers',          icon: 'work' },
    { key: 'contact' as PageKey,         label: 'Contact',         desc: 'Contact info + lead capture form',      href: '/contact',          icon: 'mail' },
    { key: 'library-preview' as PageKey, label: 'Library Preview', desc: 'Public library item listing',           href: '/library-preview',  icon: 'book' },
    { key: 'footer-nav' as PageKey,      label: 'Footer & Nav',    desc: 'Navigation links + footer text',        href: null,                icon: 'layout' },
];

const pageEnabled = ref<Record<PageKey, boolean>>({
    'home': true,
    'services': props.settings.page_enabled?.['services'] ?? true,
    'about': props.settings.page_enabled?.['about'] ?? true,
    'careers': props.settings.page_enabled?.['careers'] ?? true,
    'contact': props.settings.page_enabled?.['contact'] ?? true,
    'library-preview': props.settings.page_enabled?.['library-preview'] ?? true,
    'footer-nav': props.settings.page_enabled?.['footer-nav'] ?? true,
});

// Active page (null = show page list)
const activePage = ref<PageKey | null>(null);
const showPreview = ref(true);

const previewPages = new Set<PageKey>(['home', 'services', 'about', 'careers']);
const hasPreview = computed(() => showPreview.value && activePage.value !== null && previewPages.has(activePage.value));

// ── Confirmation dialog state ────────────────────────────────────────────────
const confirmDialog = ref<{ open: boolean; pageKey: PageKey | null; enabling: boolean }>({
    open: false, pageKey: null, enabling: false,
});

function requestToggle(key: PageKey): void {
    confirmDialog.value = { open: true, pageKey: key, enabling: !pageEnabled.value[key] };
}

function cancelToggle(): void {
    confirmDialog.value = { open: false, pageKey: null, enabling: false };
}

function confirmToggle(): void {
    const key = confirmDialog.value.pageKey;
    if (!key) { return; }

    pageEnabled.value[key] = confirmDialog.value.enabling;
    confirmDialog.value = { open: false, pageKey: null, enabling: false };

    // Persist immediately to DB
    router.put('/admin/settings/landing', {
        page_enabled: { ...pageEnabled.value },
    } as Record<string, unknown>, {
        preserveScroll: true,
        preserveState: true,
        only: [],
    });
}

// ── Field state ───────────────────────────────────────────────────────────────
const heroTitle      = ref(props.settings.hero_title ?? '');
const heroSubtitle   = ref(props.settings.hero_subtitle ?? '');
const ctaLabel       = ref(props.settings.cta_label ?? '');
const navLinks       = ref<NavLink[]>(parseJson(props.settings.nav_links, []));
const services       = ref<Service[]>(parseJson(props.settings.services, []));
const aboutText      = ref(props.settings.about_text ?? '');
const careers        = ref<Career[]>(parseJson(props.settings.careers, []));
const contactEmail   = ref(props.settings.contact_email ?? '');
const contactPhone   = ref(props.settings.contact_phone ?? '');
const contactAddress = ref(props.settings.contact_address ?? '');
const footerText     = ref(props.settings.footer_text ?? '');

function addNav()                  { navLinks.value.push({ label: '', href: '/' }); }
function removeNav(i: number)      { navLinks.value.splice(i, 1); }
function addService()              { services.value.push({ icon: 'briefcase', title: '', body: '', link_to: '' }); }
function removeService(i: number)  { services.value.splice(i, 1); }
function addCareer()               { careers.value.push({ title: '', location: '', type: 'Full-time', description: '', link_to: '' }); }
function removeCareer(i: number)   { careers.value.splice(i, 1); }

const saving = ref(false);
function save() {
    saving.value = true;
    router.put('/admin/settings/landing', {
        hero_title: heroTitle.value, hero_subtitle: heroSubtitle.value, cta_label: ctaLabel.value,
        about_text: aboutText.value, footer_text: footerText.value,
        contact_email: contactEmail.value, contact_phone: contactPhone.value, contact_address: contactAddress.value,
        nav_links: navLinks.value, services: services.value, careers: careers.value,
        page_enabled: pageEnabled.value,
    }, { onFinish: () => { saving.value = false; }, preserveScroll: true });
}

// ── Icon SVG paths ────────────────────────────────────────────────────────────
const serviceIconPaths: Record<string, string> = {
    briefcase:  'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    users:      'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    book:       'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    graduation: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
    chart:      'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    star:       'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
};

const pageIconPaths: Record<string, string> = {
    home:   'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    grid:   'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    info:   'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    work:   'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    mail:   'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    book:   'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    layout: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zm12-1a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z',
};
</script>

<template>
    <Head title="Landing CMS" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">

        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app bg-app-bg">
            <div class="flex items-center gap-3">
                <button
                    v-if="activePage !== null"
                    @click="activePage = null"
                    class="flex items-center gap-1.5 text-sm text-app-muted hover:text-white border border-app hover:border-slate-500 px-3 py-1.5 rounded-lg transition-colors"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Pages
                </button>
                <div>
                    <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Admin › Settings</p>
                    <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">
                        {{ activePage === null ? 'Landing Site CMS' : pageList.find(p => p.key === activePage)?.label }}
                    </h1>

                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Preview toggle (only on content pages) -->
                <button
                    v-if="activePage !== null && previewPages.has(activePage)"
                    @click="showPreview = !showPreview"
                    class="flex items-center gap-2 text-sm border px-4 py-2 rounded-lg transition-colors"
                    :class="showPreview ? 'text-[var(--primary)] border-[var(--primary)]/40 bg-[var(--primary)]/5' : 'text-slate-400 border-slate-700 hover:text-slate-200'"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ showPreview ? 'Hide preview' : 'Show preview' }}
                </button>
                <!-- Live site link for active page -->
                <a
                    v-if="activePage !== null && pageList.find((p) => p.key === activePage)?.href"
                    :href="pageList.find((p) => p.key === activePage)!.href!"
                    target="_blank"
                    class="flex items-center gap-2 text-sm text-app-muted hover:text-white border border-app hover:border-slate-500 px-4 py-2 rounded-lg transition-colors"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    View live
                </a>
                <a v-if="activePage === null" href="/" target="_blank" class="flex items-center gap-2 text-sm text-app-muted hover:text-white border border-app hover:border-slate-500 px-4 py-2 rounded-lg transition-colors">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    Live site
                </a>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- PAGE LIST VIEW — compact nav-list style                             -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div v-if="activePage === null" class="px-6 py-6 max-w-3xl">
            <p class="text-xs text-app-muted uppercase tracking-widest font-semibold mb-4">Pages · matches public site navigation order</p>

            <div class="rounded-xl border border-app bg-app-surface overflow-hidden divide-y divide-app">
                <div
                    v-for="(pg, index) in pageList"
                    :key="pg.key"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-app-elevated/30 transition-colors group"
                >
                    <!-- Page icon + title -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-xl bg-slate-800 flex items-center justify-center shrink-0 group-hover:bg-[var(--primary)]/10 transition-colors">
                                <svg class="size-4 text-slate-400 group-hover:text-[var(--primary)] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="pageIconPaths[pg.icon]" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white" style="font-family: Manrope, sans-serif;">{{ pg.label }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ pg.desc }}</p>
                            </div>
                        </div>
                        <!-- Enabled toggle (not for Home) -->
                        <button
                            v-if="!pg.always"
                            @click="requestToggle(pg.key)"
                            :title="pageEnabled[pg.key] ? 'Disable page' : 'Enable page'"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200"
                            :class="pageEnabled[pg.key] ? 'bg-[var(--primary)]' : 'bg-slate-700'"
                        >
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" /></svg>
                        </button>
                        <button
                            @click="activePage = pg.key"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-[var(--primary)] border border-[var(--primary)]/30 hover:bg-[var(--primary)]/5 py-1.5 rounded-lg transition-colors"
                        >
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                    </div>

                    <!-- Page icon -->
                    <div class="size-8 rounded-lg bg-app-elevated group-hover:bg-[var(--primary)]/10 flex items-center justify-center shrink-0 transition-colors">
                        <svg class="size-3.5 text-app-muted group-hover:text-[var(--primary)] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="pageIconPaths[pg.icon]" />
                        </svg>
                    </div>

                    <!-- Page name + slug -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-app leading-tight" style="font-family: Manrope, sans-serif;">{{ pg.label }}</p>
                        <p class="text-xs text-app-muted font-mono mt-0.5">{{ pg.href ?? '(no public route)' }}</p>
                    </div>

                    <!-- Enabled / Disabled badge -->
                    <div class="shrink-0">
                        <!-- Home always-on lock badge -->
                        <span v-if="pg.always" class="flex items-center gap-1 text-xs text-app-muted bg-app-elevated px-2.5 py-1 rounded-full font-medium">
                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            Always on
                        </span>
                        <!-- Toggleable badge -->
                        <button
                            v-else
                            @click="requestToggle(pg.key)"
                            class="flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-semibold border transition-colors"
                            :class="pageEnabled[pg.key]
                                ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20 hover:bg-emerald-500/20'
                                : 'text-app-muted bg-app-elevated border-app hover:border-slate-500'"
                        >
                            <!-- Enabled: checkmark -->
                            <svg v-if="pageEnabled[pg.key]" class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <!-- Disabled: dash -->
                            <svg v-else class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                            </svg>
                            {{ pageEnabled[pg.key] ? 'Enabled' : 'Disabled' }}
                        </button>
                    </div>

                    <!-- Edit button -->
                    <button
                        @click="activePage = pg.key"
                        class="flex items-center gap-1.5 text-xs font-semibold text-[var(--primary)] border border-[var(--primary)]/30 hover:bg-[var(--primary)]/5 px-3 py-1.5 rounded-lg transition-colors shrink-0"
                    >
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit
                    </button>

                    <!-- View live link -->
                    <a
                        v-if="pg.href"
                        :href="pg.href"
                        target="_blank"
                        class="flex items-center justify-center text-app-muted hover:text-app-muted transition-colors shrink-0"
                        title="View live page"
                    >
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- ── Toggle confirmation dialog ──────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="confirmDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
                <div class="bg-app-surface border border-app rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="size-10 rounded-xl flex items-center justify-center shrink-0"
                             :class="confirmDialog.enabling ? 'bg-emerald-500/10' : 'bg-red-500/10'">
                            <svg class="size-5" :class="confirmDialog.enabling ? 'text-emerald-400' : 'text-red-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="confirmDialog.enabling" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">
                                {{ confirmDialog.enabling ? 'Enable page?' : 'Disable page?' }}
                            </p>
                            <p class="text-xs text-app-muted mt-0.5">
                                {{ pageList.find((p) => p.key === confirmDialog!.key)?.label }} · {{ pageList.find((p) => p.key === confirmDialog!.key)?.href }}
                            </p>
                        </div>
                    </div>
                    <p class="text-sm text-app-muted mb-6">
                        {{ confirmDialog.enabling
                            ? 'This page will become publicly accessible immediately.'
                            : 'This page will be hidden from public visitors immediately.' }}
                    </p>
                    <div class="flex items-center gap-3">
                        <button
                            @click="confirmToggle"
                            class="flex-1 text-sm font-semibold py-2.5 rounded-lg transition-colors"
                            :class="confirmDialog.enabling
                                ? 'bg-emerald-500 hover:bg-emerald-400 text-app'
                                : 'bg-red-500/80 hover:bg-red-500 text-app'"
                        >
                            {{ confirmDialog.enabling ? 'Enable' : 'Disable' }}
                        </button>
                        <button
                            @click="cancelToggle"
                            class="flex-1 text-sm font-semibold py-2.5 rounded-lg border border-app text-app-muted hover:text-white hover:border-slate-500 transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- PAGE EDITOR VIEW                                                    -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div v-if="activePage !== null" class="flex flex-1 min-h-0" :class="hasPreview ? 'divide-x divide-app' : ''">

            <!-- Editor pane -->
            <div class="flex flex-col gap-6 px-6 py-6 overflow-y-auto" :class="hasPreview ? 'w-1/2' : 'w-full max-w-4xl'">

                <!-- HOME: Hero section -->
                <div v-if="activePage === 'home'" class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-6 py-4 border-b border-app">
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Hero Section</h2>
                        <p class="text-xs text-app-muted mt-0.5">Main headline shown to visitors on the landing page.</p>
                    </div>
                    <div class="px-6 py-6 flex flex-col gap-5">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Headline</label>
                            <input v-model="heroTitle" class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors" placeholder="Build, Learn & Grow..." />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold text-app-muted uppercase tracking-wider">Subheadline</label>
                            <RichEditor v-model="heroSubtitle" placeholder="The all-in-one platform..." minHeight="90px" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">CTA Button Label</label>
                            <input v-model="ctaLabel" class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors" placeholder="Get started free" />
                        </div>
                    </div>
                </div>

                <!-- SERVICES -->
                <div v-if="activePage === 'services'" class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-6 py-4 border-b border-app flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Services / Features</h2>
                            <p class="text-xs text-app-muted mt-0.5">Feature cards shown on the /services page.</p>
                        </div>
                        <button @click="addService" class="flex items-center gap-1.5 text-xs font-semibold text-[var(--primary)] border border-[var(--primary)]/30 hover:bg-[var(--primary)]/5 px-3 py-1.5 rounded-lg transition-colors">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add service
                        </button>
                    </div>
                    <div class="px-6 py-4 flex flex-col gap-4">
                        <div v-if="services.length === 0" class="text-center py-8 text-app-muted text-sm">No services yet. Add one above.</div>
                        <div v-for="(svc, i) in services" :key="i" class="rounded-lg border border-app p-4 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-1 flex-1">
                                    <label class="text-xs text-slate-500 uppercase tracking-wider">Icon name</label>
                                    <input v-model="svc.icon" placeholder="briefcase" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)]" />
                                </div>
                                <div class="flex flex-col gap-1 flex-1">
                                    <label class="text-xs text-slate-500 uppercase tracking-wider">Title</label>
                                    <input v-model="svc.title" placeholder="Project Management" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)]" />
                                </div>
                                <button @click="removeService(i)" class="self-end mb-1 text-app-muted hover:text-red-400 transition-colors">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <textarea v-model="svc.body" rows="2" placeholder="Description..." class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] resize-none" />
                        </div>
                    </div>
                </div>

                <!-- ABOUT -->
                <div v-if="activePage === 'about'" class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-6 py-4 border-b border-app">
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">About Page</h2>
                        <p class="text-xs text-app-muted mt-0.5">Content shown on the /about public page.</p>
                    </div>
                    <div class="px-6 py-6">
                        <RichEditor v-model="aboutText" placeholder="Write your about content here..." minHeight="200px" />
                    </div>
                </div>

                <!-- CAREERS -->
                <div v-if="activePage === 'careers'" class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-6 py-4 border-b border-app flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Careers / Open Roles</h2>
                            <p class="text-xs text-app-muted mt-0.5">Job listings shown on the /careers public page.</p>
                        </div>
                        <button @click="addCareer" class="flex items-center gap-1.5 text-xs font-semibold text-[var(--primary)] border border-[var(--primary)]/30 hover:bg-[var(--primary)]/5 px-3 py-1.5 rounded-lg transition-colors">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add role
                        </button>
                    </div>
                    <div class="px-6 py-4 flex flex-col gap-4">
                        <div v-if="careers.length === 0" class="text-center py-8 text-app-muted text-sm">No open roles yet. Add one above.</div>
                        <div v-for="(job, i) in careers" :key="i" class="rounded-lg border border-app p-4 flex flex-col gap-3">
                            <div class="flex items-start gap-3">
                                <div class="flex flex-col gap-1 flex-1">
                                    <label class="text-xs text-slate-500 uppercase tracking-wider">Job title</label>
                                    <input v-model="job.title" placeholder="Senior Developer" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)]" />
                                </div>
                                <div class="flex flex-col gap-1 w-28">
                                    <label class="text-xs text-slate-500 uppercase tracking-wider">Location</label>
                                    <input v-model="job.location" placeholder="Remote" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)]" />
                                </div>
                                <div class="flex flex-col gap-1 w-24">
                                    <label class="text-xs text-slate-500 uppercase tracking-wider">Type</label>
                                    <input v-model="job.type" placeholder="Full-time" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)]" />
                                </div>
                                <button @click="removeCareer(i)" class="self-end mb-1 text-app-muted hover:text-red-400 transition-colors">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <textarea v-model="job.description" rows="2" placeholder="Role description..." class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] resize-none" />
                        </div>
                    </div>
                </div>

                <!-- CONTACT -->
                <div v-if="activePage === 'contact'" class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-6 py-4 border-b border-app">
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Contact Information</h2>
                        <p class="text-xs text-app-muted mt-0.5">Displayed on the /contact public page.</p>
                    </div>
                    <div class="px-6 py-6 flex flex-col gap-5">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</label>
                            <input v-model="contactEmail" type="email" placeholder="hello@fluxhaven.com" class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Phone</label>
                            <input v-model="contactPhone" placeholder="+1 (555) 000-0000" class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Address</label>
                            <textarea v-model="contactAddress" rows="3" placeholder="123 Main St, City, Country" class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors resize-none" />
                        </div>
                    </div>
                </div>

                <!-- LIBRARY PREVIEW -->
                <div v-if="activePage === 'library-preview'" class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-6 py-4 border-b border-app">
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Library Preview Page</h2>
                        <p class="text-xs text-app-muted mt-0.5">Auto-generated from published library items. Manage items in the Library module.</p>
                    </div>
                    <div class="px-6 py-8 flex flex-col items-center gap-3 text-center">
                        <div class="size-12 rounded-xl bg-app-elevated flex items-center justify-center">
                            <svg class="size-6 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="pageIconPaths.book" /></svg>
                        </div>
                        <p class="text-sm text-slate-400">Library preview content is pulled automatically from your published library items. No manual editing needed here.</p>
                        <a href="/library-preview" target="_blank" class="text-xs text-[var(--primary)] hover:underline">View live page →</a>
                    </div>
                </div>

                <!-- FOOTER & NAVIGATION -->
                <template v-if="activePage === 'footer-nav'">
                    <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                        <div class="px-6 py-4 border-b border-app flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Navigation Links</h2>
                                <p class="text-xs text-app-muted mt-0.5">Header nav links shown on all public pages.</p>
                            </div>
                            <button @click="addNav" class="flex items-center gap-1.5 text-xs font-semibold text-[var(--primary)] border border-[var(--primary)]/30 hover:bg-[var(--primary)]/5 px-3 py-1.5 rounded-lg transition-colors">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add link
                            </button>
                        </div>
                        <div class="px-6 py-4 flex flex-col gap-3">
                            <div v-if="navLinks.length === 0" class="text-center py-8 text-app-muted text-sm">No nav links yet. Add one above.</div>
                            <div v-for="(link, i) in navLinks" :key="i" class="flex items-center gap-3">
                                <input v-model="link.label" placeholder="Label" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20" />
                                <input v-model="link.href" placeholder="/path" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20" />
                                <button @click="removeNav(i)" class="text-slate-600 hover:text-red-400 transition-colors">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                        <div class="px-6 py-4 border-b border-app">
                            <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Footer</h2>
                            <p class="text-xs text-app-muted mt-0.5">Copyright line shown at the bottom of every public page.</p>
                        </div>
                        <div class="px-6 py-6">
                            <input v-model="footerText" placeholder="© 2025 FluxHaven. All rights reserved." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors" />
                        </div>
                    </div>
                </template>

                <!-- Save bar -->
                <div class="flex items-center gap-4 pb-4">
                    <button @click="save" :disabled="saving" class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                        <svg v-if="saving" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <svg v-else class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Save changes
                    </button>
                    <button @click="activePage = null" class="text-sm text-app-muted hover:text-app-muted transition-colors">← Back to pages</button>
                </div>
            </div>

            <!-- Live preview pane -->
            <div v-if="hasPreview" class="w-1/2 flex flex-col bg-app-bg overflow-y-auto">
                <div class="flex items-center justify-between px-5 py-3 border-b border-app bg-app-elevated sticky top-0 z-10">
                    <div class="flex items-center gap-2">
                        <div class="size-2.5 rounded-full bg-red-500/60" /><div class="size-2.5 rounded-full bg-yellow-500/60" /><div class="size-2.5 rounded-full bg-green-500/60" />
                        <span class="text-xs text-app-muted ml-2 font-mono">live preview</span>
                    </div>
                    <span class="text-xs text-[var(--primary)]/60 font-semibold uppercase tracking-wider">Live</span>
                </div>

                <!-- Home / Hero preview -->
                <div v-if="activePage === 'home'" class="flex flex-col items-center justify-center min-h-[380px] px-10 py-16 text-center" style="background: linear-gradient(135deg, #051424 0%, #0a1929 50%, #051424 100%);">
                    <h1 class="text-3xl font-extrabold text-white mb-4 leading-tight" style="font-family: Manrope, sans-serif;">{{ heroTitle || 'Your headline goes here' }}</h1>
                    <div class="text-base text-slate-400 max-w-md leading-relaxed mb-8 preview-richtext" v-html="heroSubtitle || '<p>Your subheadline goes here</p>'" />
                    <button class="bg-[var(--primary)] text-white font-semibold px-6 py-3 rounded-xl text-sm shadow-lg">{{ ctaLabel || 'Get started free' }}</button>
                </div>

                <!-- Services preview -->
                <div v-if="activePage === 'services'" class="px-6 py-10">
                    <h2 class="text-xl font-extrabold text-app text-center mb-8" style="font-family: Manrope, sans-serif;">Services</h2>
                    <div v-if="services.length === 0" class="text-center text-app-muted text-sm py-12">Add services to see a preview.</div>
                    <div v-else class="grid grid-cols-2 gap-4">
                        <div v-for="(svc, i) in services" :key="i" class="rounded-xl border border-slate-800 bg-[#0d1c2d] p-5">
                            <div class="size-10 rounded-lg bg-[var(--primary)]/10 flex items-center justify-center mb-4">
                                <svg class="size-5 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="serviceIconPaths[svc.icon] ?? serviceIconPaths['briefcase']" /></svg>
                            </div>
                            <h3 class="text-sm font-bold text-app mb-1" style="font-family: Manrope, sans-serif;">{{ svc.title || 'Service title' }}</h3>
                            <p class="text-xs text-app-muted leading-relaxed">{{ svc.body || 'Service description' }}</p>
                        </div>
                    </div>
                </div>

                <!-- About preview -->
                <div v-if="activePage === 'about'" class="px-8 py-10">
                    <h2 class="text-xl font-extrabold text-app mb-6" style="font-family: Manrope, sans-serif;">About Us</h2>
                    <div v-if="aboutText" class="text-sm text-app-muted leading-relaxed preview-richtext" v-html="aboutText" />
                    <p v-else class="text-app-muted text-sm italic">Start writing about content to see a preview.</p>
                </div>

                <!-- Careers preview -->
                <div v-if="activePage === 'careers'" class="px-6 py-10">
                    <h2 class="text-xl font-extrabold text-app text-center mb-8" style="font-family: Manrope, sans-serif;">Open Roles</h2>
                    <div v-if="careers.length === 0" class="text-center text-app-muted text-sm py-12">Add open roles to see a preview.</div>
                    <div v-else class="flex flex-col gap-4">
                        <div v-for="(job, i) in careers" :key="i" class="rounded-xl border border-app bg-app-surface p-5">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-sm font-bold text-white" style="font-family: Manrope, sans-serif;">{{ job.title || 'Job title' }}</h3>
                                <span v-if="job.type" class="text-xs text-[var(--primary)] bg-[var(--primary)]/10 border border-[var(--primary)]/20 px-2 py-0.5 rounded-full font-semibold">{{ job.type }}</span>
                            </div>
                            <p v-if="job.location" class="text-xs text-app-muted mb-3">📍 {{ job.location }}</p>
                            <p class="text-xs text-app-muted leading-relaxed">{{ job.description || 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Confirmation dialog -->
    <Teleport to="body">
        <div v-if="confirmDialog.open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="cancelToggle" />
            <!-- Dialog -->
            <div class="relative w-full max-w-sm rounded-2xl border border-slate-700 bg-[#0d1c2d] p-6 shadow-2xl" style="font-family: Inter, sans-serif;">
                <div class="mb-4 flex items-start gap-3">
                    <div class="size-10 rounded-xl flex items-center justify-center shrink-0"
                        :class="confirmDialog.enabling ? 'bg-green-500/10 border border-green-500/20' : 'bg-red-500/10 border border-red-500/20'">
                        <svg v-if="confirmDialog.enabling" class="size-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg v-else class="size-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white" style="font-family: Manrope, sans-serif;">
                            {{ confirmDialog.enabling ? 'Enable page?' : 'Disable page?' }}
                        </h3>
                        <p class="mt-1 text-xs text-slate-400 leading-relaxed">
                            <span v-if="confirmDialog.enabling">
                                The <strong class="text-slate-200">{{ pageList.find(p => p.key === confirmDialog.pageKey)?.label }}</strong> page will become publicly visible immediately.
                            </span>
                            <span v-else>
                                The <strong class="text-slate-200">{{ pageList.find(p => p.key === confirmDialog.pageKey)?.label }}</strong> page will be hidden from visitors. This takes effect immediately.
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button
                        @click="cancelToggle"
                        class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmToggle"
                        class="px-4 py-2 text-xs font-semibold text-white rounded-lg transition-colors"
                        :class="confirmDialog.enabling ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500'"
                    >
                        {{ confirmDialog.enabling ? 'Enable' : 'Disable' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.preview-richtext :deep(h2) { font-size: 1.1em; font-weight: 700; margin: 0.5em 0 0.25em; color: #fff; }
.preview-richtext :deep(h3) { font-size: 1em; font-weight: 600; margin: 0.5em 0 0.25em; color: #e2e8f0; }
.preview-richtext :deep(ul) { list-style: disc; padding-left: 1.25em; margin: 0.25em 0; }
.preview-richtext :deep(ol) { list-style: decimal; padding-left: 1.25em; margin: 0.25em 0; }
.preview-richtext :deep(p)  { margin: 0.15em 0; }
.preview-richtext :deep(strong) { color: #f1f5f9; }
</style>
