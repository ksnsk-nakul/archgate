<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import RichEditor from '@/components/RichEditor.vue';

type NavLink = { label: string; href: string };
type Service = { icon: string; title: string; body: string; link_to?: string };
type Career = {
    title: string;
    location?: string;
    type?: string;
    description?: string;
    link_to?: string;
};

type LandingSettings = {
    hero_title: string;
    hero_subtitle: string;
    cta_label: string;
    about_text: string;
    footer_text: string;
    contact_email: string;
    contact_phone: string;
    contact_address: string;
    nav_links: string;
    services: string;
    careers: string;
    page_enabled?: Record<string, boolean>;
};

const props = defineProps<{ settings: LandingSettings }>();

const parseJson = <T,>(val: string | null | undefined, fallback: T): T => {
    try {
        return val ? JSON.parse(val) : fallback;
    } catch {
        return fallback;
    }
};

// ── Page list definition ──────────────────────────────────────────────────────
type PageKey =
    | 'home'
    | 'services'
    | 'about'
    | 'careers'
    | 'contact'
    | 'library-preview'
    | 'footer-nav';
type PageDef = {
    key: PageKey;
    label: string;
    desc: string;
    href: string | null;
    icon: string;
    always?: boolean;
};

const pageList = ref<PageDef[]>([
    {
        key: 'home',
        label: 'Home',
        desc: 'Hero, services teaser, call-to-action',
        href: '/',
        icon: 'home',
        always: true,
    },
    {
        key: 'services',
        label: 'Services',
        desc: 'Feature / services grid',
        href: '/services',
        icon: 'grid',
    },
    {
        key: 'about',
        label: 'About',
        desc: 'Rich text about section',
        href: '/about',
        icon: 'info',
    },
    {
        key: 'careers',
        label: 'Careers',
        desc: 'Open job listings',
        href: '/careers',
        icon: 'work',
    },
    {
        key: 'contact',
        label: 'Contact',
        desc: 'Contact info + lead capture form',
        href: '/contact',
        icon: 'mail',
    },
    {
        key: 'library-preview',
        label: 'Library Preview',
        desc: 'Public library item listing',
        href: '/library-preview',
        icon: 'book',
    },
    {
        key: 'footer-nav',
        label: 'Footer & Nav',
        desc: 'Navigation links + footer text',
        href: null,
        icon: 'layout',
    },
]);

const pageEnabled = ref<Record<PageKey, boolean>>({
    home: true,
    services: props.settings.page_enabled?.['services'] ?? true,
    about: props.settings.page_enabled?.['about'] ?? true,
    careers: props.settings.page_enabled?.['careers'] ?? true,
    contact: props.settings.page_enabled?.['contact'] ?? true,
    'library-preview': props.settings.page_enabled?.['library-preview'] ?? true,
    'footer-nav': props.settings.page_enabled?.['footer-nav'] ?? true,
});

// Active page (null = show page list)
const activePage = ref<PageKey | null>(null);
const showPreview = ref(true);

const previewPages = new Set<PageKey>(['home', 'services', 'about', 'careers']);
const hasPreview = computed(
    () =>
        showPreview.value &&
        activePage.value !== null &&
        previewPages.has(activePage.value),
);

// ── Confirmation dialog state ────────────────────────────────────────────────
const confirmDialog = ref<{
    open: boolean;
    pageKey: PageKey | null;
    enabling: boolean;
}>({
    open: false,
    pageKey: null,
    enabling: false,
});

function requestToggle(key: PageKey): void {
    confirmDialog.value = {
        open: true,
        pageKey: key,
        enabling: !pageEnabled.value[key],
    };
}

function cancelToggle(): void {
    confirmDialog.value = { open: false, pageKey: null, enabling: false };
}

function confirmToggle(): void {
    const key = confirmDialog.value.pageKey;
    if (!key) {
        return;
    }

    pageEnabled.value[key] = confirmDialog.value.enabling;
    confirmDialog.value = { open: false, pageKey: null, enabling: false };

    // Persist immediately to DB
    router.put(
        '/admin/settings/landing',
        {
            page_enabled: { ...pageEnabled.value },
        } as Record<string, unknown>,
        {
            preserveScroll: true,
            preserveState: true,
            only: [],
        },
    );
}

// ── Field state ───────────────────────────────────────────────────────────────
const heroTitle = ref(props.settings.hero_title ?? '');
const heroSubtitle = ref(props.settings.hero_subtitle ?? '');
const ctaLabel = ref(props.settings.cta_label ?? '');
const navLinks = ref<NavLink[]>(parseJson(props.settings.nav_links, []));
const services = ref<Service[]>(parseJson(props.settings.services, []));
const aboutText = ref(props.settings.about_text ?? '');
const careers = ref<Career[]>(parseJson(props.settings.careers, []));
const contactEmail = ref(props.settings.contact_email ?? '');
const contactPhone = ref(props.settings.contact_phone ?? '');
const contactAddress = ref(props.settings.contact_address ?? '');
const footerText = ref(props.settings.footer_text ?? '');

function addNav() {
    navLinks.value.push({ label: '', href: '/' });
}
function removeNav(i: number) {
    navLinks.value.splice(i, 1);
}
function addService() {
    services.value.push({
        icon: 'briefcase',
        title: '',
        body: '',
        link_to: '',
    });
}
function removeService(i: number) {
    services.value.splice(i, 1);
}
function addCareer() {
    careers.value.push({
        title: '',
        location: '',
        type: 'Full-time',
        description: '',
        link_to: '',
    });
}
function removeCareer(i: number) {
    careers.value.splice(i, 1);
}

const saving = ref(false);
function save() {
    saving.value = true;
    router.put(
        '/admin/settings/landing',
        {
            hero_title: heroTitle.value,
            hero_subtitle: heroSubtitle.value,
            cta_label: ctaLabel.value,
            about_text: aboutText.value,
            footer_text: footerText.value,
            contact_email: contactEmail.value,
            contact_phone: contactPhone.value,
            contact_address: contactAddress.value,
            nav_links: navLinks.value,
            services: services.value,
            careers: careers.value,
            page_enabled: pageEnabled.value,
        },
        {
            onFinish: () => {
                saving.value = false;
            },
            preserveScroll: true,
        },
    );
}

// ── Icon SVG paths ────────────────────────────────────────────────────────────
const serviceIconPaths: Record<string, string> = {
    briefcase:
        'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    users: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    book: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    graduation:
        'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
    chart: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    star: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
};

const pageIconPaths: Record<string, string> = {
    home: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    grid: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    work: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    mail: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    book: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    layout: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zm12-1a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z',
};
</script>

<template>
    <Head title="Landing CMS" />

    <div
        class="bg-app-bg text-app flex min-h-full flex-col"
        style="font-family: Inter, sans-serif"
    >
        <!-- Toolbar -->
        <div
            class="border-app bg-app-bg flex flex-wrap items-center justify-between gap-3 border-b px-4 py-3 md:px-6 md:py-4"
        >
            <div class="flex items-center gap-3">
                <button
                    v-if="activePage !== null"
                    @click="activePage = null"
                    class="text-app-muted border-app flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-sm transition-colors hover:border-slate-500 hover:text-white"
                >
                    <svg
                        class="size-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                    Pages
                </button>
                <div>
                    <p
                        class="text-app-muted mb-0.5 text-xs font-semibold tracking-widest uppercase"
                    >
                        Admin › Settings
                    </p>
                    <h1
                        class="text-app text-xl font-bold"
                        style="font-family: Manrope, sans-serif"
                    >
                        {{
                            activePage === null
                                ? 'Landing Site CMS'
                                : pageList.find((p) => p.key === activePage)
                                      ?.label
                        }}
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Preview toggle (only on content pages) -->
                <button
                    v-if="activePage !== null && previewPages.has(activePage)"
                    @click="showPreview = !showPreview"
                    class="flex items-center gap-2 rounded-lg border px-4 py-2 text-sm transition-colors"
                    :class="
                        showPreview
                            ? 'border-[var(--primary)]/40 bg-[var(--primary)]/5 text-[var(--primary)]'
                            : 'border-slate-700 text-slate-400 hover:text-slate-200'
                    "
                >
                    <svg
                        class="size-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                    </svg>
                    <span class="hidden sm:inline">{{ showPreview ? 'Hide preview' : 'Show preview' }}</span>
                </button>
                <!-- Live site link for active page -->
                <a
                    v-if="activePage !== null && pageList.find((p) => p.key === activePage)?.href"
                    :href="pageList.find((p) => p.key === activePage)!.href!"
                    target="_blank"
                    class="text-app-muted border-app flex items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-colors hover:border-slate-500 hover:text-white md:px-4"
                    title="View live page"
                >
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span class="hidden sm:inline">View live</span>
                </a>
                <a
                    v-if="activePage === null"
                    href="/"
                    target="_blank"
                    class="text-app-muted border-app flex items-center gap-2 rounded-lg border px-3 py-2 text-sm transition-colors hover:border-slate-500 hover:text-white md:px-4"
                    title="Live site"
                >
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span class="hidden sm:inline">Live site</span>
                </a>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- PAGE LIST VIEW — compact nav-list style                             -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div v-if="activePage === null" class="max-w-3xl px-6 py-6">
            <p
                class="text-app-muted mb-4 text-xs font-semibold tracking-widest uppercase"
            >
                Pages · matches public site navigation order
            </p>

            <div class="border-app bg-app-surface divide-app divide-y overflow-hidden rounded-xl border">
                <div
                    v-for="pg in pageList"
                    :key="pg.key"
                    class="hover:bg-app-elevated/40 group flex flex-col gap-2 px-4 py-3 transition-colors sm:flex-row sm:items-center sm:gap-4 sm:px-5 sm:py-3.5"
                >
                    <!-- Top row (mobile) / full row (desktop) -->
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <!-- Icon -->
                        <div class="bg-app-elevated flex size-8 shrink-0 items-center justify-center rounded-lg transition-colors group-hover:bg-[var(--primary)]/10 sm:size-9">
                            <svg class="text-app-muted size-3.5 transition-colors group-hover:text-[var(--primary)] sm:size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="pageIconPaths[pg.icon]" />
                            </svg>
                        </div>

                        <!-- Label + slug -->
                        <div class="min-w-0 flex-1">
                            <p class="text-app text-sm font-semibold leading-tight" style="font-family: Manrope, sans-serif">{{ pg.label }}</p>
                            <p class="text-app-muted mt-0.5 font-mono text-xs truncate">{{ pg.href ?? '(no public route)' }}</p>
                        </div>

                        <!-- Reorder buttons — visible on all sizes, pushed right on mobile -->
                        <div class="flex shrink-0 flex-col gap-0.5 sm:hidden">
                            <button
                                @click="pageList.splice(pageList.indexOf(pg) - 1, 0, pageList.splice(pageList.indexOf(pg), 1)[0])"
                                :disabled="pageList.indexOf(pg) === 0"
                                class="text-app-muted hover:text-app hover:bg-app-elevated flex size-5 items-center justify-center rounded transition-colors disabled:cursor-not-allowed disabled:opacity-20"
                            >
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" /></svg>
                            </button>
                            <button
                                @click="pageList.splice(pageList.indexOf(pg) + 1, 0, pageList.splice(pageList.indexOf(pg), 1)[0])"
                                :disabled="pageList.indexOf(pg) === pageList.length - 1"
                                class="text-app-muted hover:text-app hover:bg-app-elevated flex size-5 items-center justify-center rounded transition-colors disabled:cursor-not-allowed disabled:opacity-20"
                            >
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom row on mobile / right side on desktop -->
                    <div class="flex items-center gap-2 sm:gap-3">

                        <!-- Reorder buttons — desktop only -->
                        <div class="hidden shrink-0 flex-col gap-0.5 sm:flex">
                            <button
                                @click="pageList.splice(pageList.indexOf(pg) - 1, 0, pageList.splice(pageList.indexOf(pg), 1)[0])"
                                :disabled="pageList.indexOf(pg) === 0"
                                class="text-app-muted hover:text-app hover:bg-app-elevated flex size-5 items-center justify-center rounded transition-colors disabled:cursor-not-allowed disabled:opacity-20"
                            >
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" /></svg>
                            </button>
                            <button
                                @click="pageList.splice(pageList.indexOf(pg) + 1, 0, pageList.splice(pageList.indexOf(pg), 1)[0])"
                                :disabled="pageList.indexOf(pg) === pageList.length - 1"
                                class="text-app-muted hover:text-app hover:bg-app-elevated flex size-5 items-center justify-center rounded transition-colors disabled:cursor-not-allowed disabled:opacity-20"
                            >
                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                        </div>

                        <!-- Status badge -->
                        <span v-if="pg.always" class="text-app-muted bg-app-elevated border-app inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium whitespace-nowrap">
                            <svg class="size-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            Always on
                        </span>
                        <button
                            v-else
                            @click="requestToggle(pg.key)"
                            class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold transition-colors whitespace-nowrap sm:px-3 sm:py-1.5"
                            :class="pageEnabled[pg.key]
                                ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20'
                                : 'border-app bg-app-elevated text-slate-500 hover:border-slate-600'"
                        >
                            <svg v-if="pageEnabled[pg.key]" class="size-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            <svg v-else class="size-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" /></svg>
                            {{ pageEnabled[pg.key] ? 'Enabled' : 'Disabled' }}
                        </button>

                        <!-- Edit button -->
                        <button
                            @click="activePage = pg.key"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-[var(--primary)]/30 px-2.5 py-1 text-xs font-semibold text-[var(--primary)] transition-colors hover:bg-[var(--primary)]/5 sm:px-3 sm:py-1.5"
                        >
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit
                        </button>

                        <!-- View live link -->
                        <a v-if="pg.href" :href="pg.href" target="_blank"
                            class="text-app-muted hover:text-app hover:bg-app-elevated flex size-7 shrink-0 items-center justify-center rounded-lg transition-colors"
                            title="View live page"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        </a>
                        <div v-else class="size-7 shrink-0 sm:block hidden" />
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Toggle confirmation dialog ──────────────────────────────────── -->
        <Teleport to="body">
            <div
                v-if="confirmDialog.open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
            >
                <div
                    class="bg-app-surface border-app mx-4 w-full max-w-sm rounded-2xl border p-6 shadow-2xl"
                >
                    <div class="mb-4 flex items-center gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl"
                            :class="
                                confirmDialog.enabling
                                    ? 'bg-emerald-500/10'
                                    : 'bg-red-500/10'
                            "
                        >
                            <svg
                                class="size-5"
                                :class="
                                    confirmDialog.enabling
                                        ? 'text-emerald-400'
                                        : 'text-red-400'
                                "
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    v-if="confirmDialog.enabling"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                                <path
                                    v-else
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p
                                class="text-app text-sm font-bold"
                                style="font-family: Manrope, sans-serif"
                            >
                                {{
                                    confirmDialog.enabling
                                        ? 'Enable page?'
                                        : 'Disable page?'
                                }}
                            </p>
                            <p class="text-app-muted mt-0.5 text-xs">
                                {{
                                    pageList.find(
                                        (p) => p.key === confirmDialog.pageKey,
                                    )?.label
                                }}
                                ·
                                {{
                                    pageList.find(
                                        (p) => p.key === confirmDialog.pageKey,
                                    )?.href
                                }}
                            </p>
                        </div>
                    </div>
                    <p class="text-app-muted mb-6 text-sm">
                        {{
                            confirmDialog.enabling
                                ? 'This page will become publicly accessible immediately.'
                                : 'This page will be hidden from public visitors immediately.'
                        }}
                    </p>
                    <div class="flex items-center gap-3">
                        <button
                            @click="confirmToggle"
                            class="flex-1 rounded-lg py-2.5 text-sm font-semibold transition-colors"
                            :class="
                                confirmDialog.enabling
                                    ? 'text-app bg-emerald-500 hover:bg-emerald-400'
                                    : 'text-app bg-red-500/80 hover:bg-red-500'
                            "
                        >
                            {{ confirmDialog.enabling ? 'Enable' : 'Disable' }}
                        </button>
                        <button
                            @click="cancelToggle"
                            class="border-app text-app-muted flex-1 rounded-lg border py-2.5 text-sm font-semibold transition-colors hover:border-slate-500 hover:text-white"
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
        <div
            v-if="activePage !== null"
            class="flex min-h-0 flex-1"
            :class="hasPreview ? 'lg:divide-app lg:divide-x' : ''"
        >
            <!-- Editor pane -->
            <div
                class="flex min-w-0 flex-col gap-6 overflow-y-auto px-4 py-6 md:px-6"
                :class="hasPreview ? 'w-full lg:w-1/2' : 'w-full max-w-4xl'"
            >
                <!-- HOME: Hero section -->
                <div
                    v-if="activePage === 'home'"
                    class="border-app bg-app-surface overflow-hidden rounded-xl border"
                >
                    <div class="border-app border-b px-6 py-4">
                        <h2
                            class="text-app text-sm font-bold"
                            style="font-family: Manrope, sans-serif"
                        >
                            Hero Section
                        </h2>
                        <p class="text-app-muted mt-0.5 text-xs">
                            Main headline shown to visitors on the landing page.
                        </p>
                    </div>
                    <div class="flex flex-col gap-5 px-6 py-6">
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >Headline</label
                            >
                            <input
                                v-model="heroTitle"
                                class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 transition-colors focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                                placeholder="Build, Learn & Grow..."
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-app-muted text-xs font-semibold tracking-wider uppercase"
                                >Subheadline</label
                            >
                            <RichEditor
                                v-model="heroSubtitle"
                                placeholder="The all-in-one platform..."
                                minHeight="90px"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >CTA Button Label</label
                            >
                            <input
                                v-model="ctaLabel"
                                class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 transition-colors focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                                placeholder="Get started free"
                            />
                        </div>
                    </div>
                </div>

                <!-- SERVICES -->
                <div
                    v-if="activePage === 'services'"
                    class="border-app bg-app-surface overflow-hidden rounded-xl border"
                >
                    <div
                        class="border-app flex items-center justify-between border-b px-6 py-4"
                    >
                        <div>
                            <h2
                                class="text-app text-sm font-bold"
                                style="font-family: Manrope, sans-serif"
                            >
                                Services / Features
                            </h2>
                            <p class="text-app-muted mt-0.5 text-xs">
                                Feature cards shown on the /services page.
                            </p>
                        </div>
                        <button
                            @click="addService"
                            class="flex items-center gap-1.5 rounded-lg border border-[var(--primary)]/30 px-3 py-1.5 text-xs font-semibold text-[var(--primary)] transition-colors hover:bg-[var(--primary)]/5"
                        >
                            <svg
                                class="size-3.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            Add service
                        </button>
                    </div>
                    <div class="flex flex-col gap-4 px-6 py-4">
                        <div
                            v-if="services.length === 0"
                            class="text-app-muted py-8 text-center text-sm"
                        >
                            No services yet. Add one above.
                        </div>
                        <div
                            v-for="(svc, i) in services"
                            :key="i"
                            class="border-app flex flex-col gap-3 rounded-lg border p-4"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex flex-1 flex-col gap-1">
                                    <label
                                        class="text-xs tracking-wider text-slate-500 uppercase"
                                        >Icon name</label
                                    >
                                    <input
                                        v-model="svc.icon"
                                        placeholder="briefcase"
                                        class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:outline-none"
                                    />
                                </div>
                                <div class="flex flex-1 flex-col gap-1">
                                    <label
                                        class="text-xs tracking-wider text-slate-500 uppercase"
                                        >Title</label
                                    >
                                    <input
                                        v-model="svc.title"
                                        placeholder="Project Management"
                                        class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:outline-none"
                                    />
                                </div>
                                <button
                                    @click="removeService(i)"
                                    class="text-app-muted mb-1 self-end transition-colors hover:text-red-400"
                                >
                                    <svg
                                        class="size-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <textarea
                                v-model="svc.body"
                                rows="2"
                                placeholder="Description..."
                                class="resize-none rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- ABOUT -->
                <div
                    v-if="activePage === 'about'"
                    class="border-app bg-app-surface overflow-hidden rounded-xl border"
                >
                    <div class="border-app border-b px-6 py-4">
                        <h2
                            class="text-app text-sm font-bold"
                            style="font-family: Manrope, sans-serif"
                        >
                            About Page
                        </h2>
                        <p class="text-app-muted mt-0.5 text-xs">
                            Content shown on the /about public page.
                        </p>
                    </div>
                    <div class="px-6 py-6">
                        <RichEditor
                            v-model="aboutText"
                            placeholder="Write your about content here..."
                            minHeight="200px"
                        />
                    </div>
                </div>

                <!-- CAREERS -->
                <div
                    v-if="activePage === 'careers'"
                    class="border-app bg-app-surface overflow-hidden rounded-xl border"
                >
                    <div
                        class="border-app flex items-center justify-between border-b px-6 py-4"
                    >
                        <div>
                            <h2
                                class="text-app text-sm font-bold"
                                style="font-family: Manrope, sans-serif"
                            >
                                Careers / Open Roles
                            </h2>
                            <p class="text-app-muted mt-0.5 text-xs">
                                Job listings shown on the /careers public page.
                            </p>
                        </div>
                        <button
                            @click="addCareer"
                            class="flex items-center gap-1.5 rounded-lg border border-[var(--primary)]/30 px-3 py-1.5 text-xs font-semibold text-[var(--primary)] transition-colors hover:bg-[var(--primary)]/5"
                        >
                            <svg
                                class="size-3.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            Add role
                        </button>
                    </div>
                    <div class="flex flex-col gap-4 px-6 py-4">
                        <div
                            v-if="careers.length === 0"
                            class="text-app-muted py-8 text-center text-sm"
                        >
                            No open roles yet. Add one above.
                        </div>
                        <div
                            v-for="(job, i) in careers"
                            :key="i"
                            class="border-app flex flex-col gap-3 rounded-lg border p-4"
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex flex-1 flex-col gap-1">
                                    <label
                                        class="text-xs tracking-wider text-slate-500 uppercase"
                                        >Job title</label
                                    >
                                    <input
                                        v-model="job.title"
                                        placeholder="Senior Developer"
                                        class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:outline-none"
                                    />
                                </div>
                                <div class="flex w-28 flex-col gap-1">
                                    <label
                                        class="text-xs tracking-wider text-slate-500 uppercase"
                                        >Location</label
                                    >
                                    <input
                                        v-model="job.location"
                                        placeholder="Remote"
                                        class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:outline-none"
                                    />
                                </div>
                                <div class="flex w-24 flex-col gap-1">
                                    <label
                                        class="text-xs tracking-wider text-slate-500 uppercase"
                                        >Type</label
                                    >
                                    <input
                                        v-model="job.type"
                                        placeholder="Full-time"
                                        class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:outline-none"
                                    />
                                </div>
                                <button
                                    @click="removeCareer(i)"
                                    class="text-app-muted mb-1 self-end transition-colors hover:text-red-400"
                                >
                                    <svg
                                        class="size-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <textarea
                                v-model="job.description"
                                rows="2"
                                placeholder="Role description..."
                                class="resize-none rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- CONTACT -->
                <div
                    v-if="activePage === 'contact'"
                    class="border-app bg-app-surface overflow-hidden rounded-xl border"
                >
                    <div class="border-app border-b px-6 py-4">
                        <h2
                            class="text-app text-sm font-bold"
                            style="font-family: Manrope, sans-serif"
                        >
                            Contact Information
                        </h2>
                        <p class="text-app-muted mt-0.5 text-xs">
                            Displayed on the /contact public page.
                        </p>
                    </div>
                    <div class="flex flex-col gap-5 px-6 py-6">
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >Email</label
                            >
                            <input
                                v-model="contactEmail"
                                type="email"
                                placeholder="hello@fluxhaven.com"
                                class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 transition-colors focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >Phone</label
                            >
                            <input
                                v-model="contactPhone"
                                placeholder="+1 (555) 000-0000"
                                class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 transition-colors focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                            />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >Address</label
                            >
                            <textarea
                                v-model="contactAddress"
                                rows="3"
                                placeholder="123 Main St, City, Country"
                                class="resize-none rounded-lg border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 transition-colors focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- LIBRARY PREVIEW -->
                <div
                    v-if="activePage === 'library-preview'"
                    class="border-app bg-app-surface overflow-hidden rounded-xl border"
                >
                    <div class="border-app border-b px-6 py-4">
                        <h2
                            class="text-app text-sm font-bold"
                            style="font-family: Manrope, sans-serif"
                        >
                            Library Preview Page
                        </h2>
                        <p class="text-app-muted mt-0.5 text-xs">
                            Auto-generated from published library items. Manage
                            items in the Library module.
                        </p>
                    </div>
                    <div
                        class="flex flex-col items-center gap-3 px-6 py-8 text-center"
                    >
                        <div
                            class="bg-app-elevated flex size-12 items-center justify-center rounded-xl"
                        >
                            <svg
                                class="text-app-muted size-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    :d="pageIconPaths.book"
                                />
                            </svg>
                        </div>
                        <p class="text-sm text-slate-400">
                            Library preview content is pulled automatically from
                            your published library items. No manual editing
                            needed here.
                        </p>
                        <a
                            href="/library-preview"
                            target="_blank"
                            class="text-xs text-[var(--primary)] hover:underline"
                            >View live page →</a
                        >
                    </div>
                </div>

                <!-- FOOTER & NAVIGATION -->
                <template v-if="activePage === 'footer-nav'">
                    <div
                        class="border-app bg-app-surface overflow-hidden rounded-xl border"
                    >
                        <div
                            class="border-app flex items-center justify-between border-b px-6 py-4"
                        >
                            <div>
                                <h2
                                    class="text-app text-sm font-bold"
                                    style="font-family: Manrope, sans-serif"
                                >
                                    Navigation Links
                                </h2>
                                <p class="text-app-muted mt-0.5 text-xs">
                                    Header nav links shown on all public pages.
                                </p>
                            </div>
                            <button
                                @click="addNav"
                                class="flex items-center gap-1.5 rounded-lg border border-[var(--primary)]/30 px-3 py-1.5 text-xs font-semibold text-[var(--primary)] transition-colors hover:bg-[var(--primary)]/5"
                            >
                                <svg
                                    class="size-3.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                                Add link
                            </button>
                        </div>
                        <div class="flex flex-col gap-3 px-6 py-4">
                            <div
                                v-if="navLinks.length === 0"
                                class="text-app-muted py-8 text-center text-sm"
                            >
                                No nav links yet. Add one above.
                            </div>
                            <div
                                v-for="(link, i) in navLinks"
                                :key="i"
                                class="flex items-center gap-3"
                            >
                                <input
                                    v-model="link.label"
                                    placeholder="Label"
                                    class="flex-1 rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                                />
                                <input
                                    v-model="link.href"
                                    placeholder="/path"
                                    class="flex-1 rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                                />
                                <button
                                    @click="removeNav(i)"
                                    class="text-slate-600 transition-colors hover:text-red-400"
                                >
                                    <svg
                                        class="size-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div
                        class="border-app bg-app-surface overflow-hidden rounded-xl border"
                    >
                        <div class="border-app border-b px-6 py-4">
                            <h2
                                class="text-app text-sm font-bold"
                                style="font-family: Manrope, sans-serif"
                            >
                                Footer
                            </h2>
                            <p class="text-app-muted mt-0.5 text-xs">
                                Copyright line shown at the bottom of every
                                public page.
                            </p>
                        </div>
                        <div class="px-6 py-6">
                            <input
                                v-model="footerText"
                                placeholder="© 2025 FluxHaven. All rights reserved."
                                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 transition-colors focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 focus:outline-none"
                            />
                        </div>
                    </div>
                </template>

                <!-- Save bar -->
                <div class="flex items-center gap-4 pb-4">
                    <button
                        @click="save"
                        :disabled="saving"
                        class="flex items-center gap-2 rounded-lg bg-[var(--primary)] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[var(--primary-hover)] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg
                            v-if="saving"
                            class="size-4 animate-spin"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="size-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                        Save changes
                    </button>
                    <button
                        @click="activePage = null"
                        class="text-app-muted hover:text-app-muted text-sm transition-colors"
                    >
                        ← Back to pages
                    </button>
                </div>
            </div>

            <!-- Live preview pane -->
            <div
                v-if="hasPreview"
                class="bg-app-bg hidden w-1/2 flex-col overflow-y-auto lg:flex"
            >
                <div
                    class="border-app bg-app-elevated sticky top-0 z-10 flex items-center justify-between border-b px-5 py-3"
                >
                    <div class="flex items-center gap-2">
                        <div class="size-2.5 rounded-full bg-red-500/60" />
                        <div class="size-2.5 rounded-full bg-yellow-500/60" />
                        <div class="size-2.5 rounded-full bg-green-500/60" />
                        <span class="text-app-muted ml-2 font-mono text-xs"
                            >live preview</span
                        >
                    </div>
                    <span
                        class="text-xs font-semibold tracking-wider text-[var(--primary)]/60 uppercase"
                        >Live</span
                    >
                </div>

                <!-- Home / Hero preview -->
                <div
                    v-if="activePage === 'home'"
                    class="flex min-h-[380px] flex-col items-center justify-center px-10 py-16 text-center"
                    style="
                        background: linear-gradient(
                            135deg,
                            #051424 0%,
                            #0a1929 50%,
                            #051424 100%
                        );
                    "
                >
                    <h1
                        class="mb-4 text-3xl leading-tight font-extrabold text-white"
                        style="font-family: Manrope, sans-serif"
                    >
                        {{ heroTitle || 'Your headline goes here' }}
                    </h1>
                    <div
                        class="preview-richtext mb-8 max-w-md text-base leading-relaxed text-slate-400"
                        v-html="
                            heroSubtitle || '<p>Your subheadline goes here</p>'
                        "
                    />
                    <button
                        class="rounded-xl bg-[var(--primary)] px-6 py-3 text-sm font-semibold text-white shadow-lg"
                    >
                        {{ ctaLabel || 'Get started free' }}
                    </button>
                </div>

                <!-- Services preview -->
                <div v-if="activePage === 'services'" class="px-6 py-10">
                    <h2
                        class="text-app mb-8 text-center text-xl font-extrabold"
                        style="font-family: Manrope, sans-serif"
                    >
                        Services
                    </h2>
                    <div
                        v-if="services.length === 0"
                        class="text-app-muted py-12 text-center text-sm"
                    >
                        Add services to see a preview.
                    </div>
                    <div v-else class="grid grid-cols-2 gap-4">
                        <div
                            v-for="(svc, i) in services"
                            :key="i"
                            class="rounded-xl border border-slate-800 bg-[#0d1c2d] p-5"
                        >
                            <div
                                class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[var(--primary)]/10"
                            >
                                <svg
                                    class="size-5 text-[var(--primary)]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        :d="
                                            serviceIconPaths[svc.icon] ??
                                            serviceIconPaths['briefcase']
                                        "
                                    />
                                </svg>
                            </div>
                            <h3
                                class="text-app mb-1 text-sm font-bold"
                                style="font-family: Manrope, sans-serif"
                            >
                                {{ svc.title || 'Service title' }}
                            </h3>
                            <p class="text-app-muted text-xs leading-relaxed">
                                {{ svc.body || 'Service description' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- About preview -->
                <div v-if="activePage === 'about'" class="px-8 py-10">
                    <h2
                        class="text-app mb-6 text-xl font-extrabold"
                        style="font-family: Manrope, sans-serif"
                    >
                        About Us
                    </h2>
                    <div
                        v-if="aboutText"
                        class="text-app-muted preview-richtext text-sm leading-relaxed"
                        v-html="aboutText"
                    />
                    <p v-else class="text-app-muted text-sm italic">
                        Start writing about content to see a preview.
                    </p>
                </div>

                <!-- Careers preview -->
                <div v-if="activePage === 'careers'" class="px-6 py-10">
                    <h2
                        class="text-app mb-8 text-center text-xl font-extrabold"
                        style="font-family: Manrope, sans-serif"
                    >
                        Open Roles
                    </h2>
                    <div
                        v-if="careers.length === 0"
                        class="text-app-muted py-12 text-center text-sm"
                    >
                        Add open roles to see a preview.
                    </div>
                    <div v-else class="flex flex-col gap-4">
                        <div
                            v-for="(job, i) in careers"
                            :key="i"
                            class="border-app bg-app-surface rounded-xl border p-5"
                        >
                            <div class="mb-2 flex items-start justify-between">
                                <h3
                                    class="text-sm font-bold text-white"
                                    style="font-family: Manrope, sans-serif"
                                >
                                    {{ job.title || 'Job title' }}
                                </h3>
                                <span
                                    v-if="job.type"
                                    class="rounded-full border border-[var(--primary)]/20 bg-[var(--primary)]/10 px-2 py-0.5 text-xs font-semibold text-[var(--primary)]"
                                    >{{ job.type }}</span
                                >
                            </div>
                            <p
                                v-if="job.location"
                                class="text-app-muted mb-3 text-xs"
                            >
                                📍 {{ job.location }}
                            </p>
                            <p class="text-app-muted text-xs leading-relaxed">
                                {{
                                    job.description ||
                                    'No description provided.'
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation dialog -->
    <Teleport to="body">
        <div
            v-if="confirmDialog.open"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                @click="cancelToggle"
            />
            <!-- Dialog -->
            <div
                class="relative w-full max-w-sm rounded-2xl border border-slate-700 bg-[#0d1c2d] p-6 shadow-2xl"
                style="font-family: Inter, sans-serif"
            >
                <div class="mb-4 flex items-start gap-3">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl"
                        :class="
                            confirmDialog.enabling
                                ? 'border border-green-500/20 bg-green-500/10'
                                : 'border border-red-500/20 bg-red-500/10'
                        "
                    >
                        <svg
                            v-if="confirmDialog.enabling"
                            class="size-5 text-green-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                        <svg
                            v-else
                            class="size-5 text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                            />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-sm font-bold text-white"
                            style="font-family: Manrope, sans-serif"
                        >
                            {{
                                confirmDialog.enabling
                                    ? 'Enable page?'
                                    : 'Disable page?'
                            }}
                        </h3>
                        <p class="mt-1 text-xs leading-relaxed text-slate-400">
                            <span v-if="confirmDialog.enabling">
                                The
                                <strong class="text-slate-200">{{
                                    pageList.find(
                                        (p) => p.key === confirmDialog.pageKey,
                                    )?.label
                                }}</strong>
                                page will become publicly visible immediately.
                            </span>
                            <span v-else>
                                The
                                <strong class="text-slate-200">{{
                                    pageList.find(
                                        (p) => p.key === confirmDialog.pageKey,
                                    )?.label
                                }}</strong>
                                page will be hidden from visitors. This takes
                                effect immediately.
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        @click="cancelToggle"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs font-semibold text-slate-400 transition-colors hover:border-slate-500 hover:text-white"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmToggle"
                        class="rounded-lg px-4 py-2 text-xs font-semibold text-white transition-colors"
                        :class="
                            confirmDialog.enabling
                                ? 'bg-green-600 hover:bg-green-500'
                                : 'bg-red-600 hover:bg-red-500'
                        "
                    >
                        {{ confirmDialog.enabling ? 'Enable' : 'Disable' }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.preview-richtext :deep(h2) {
    font-size: 1.1em;
    font-weight: 700;
    margin: 0.5em 0 0.25em;
    color: #fff;
}
.preview-richtext :deep(h3) {
    font-size: 1em;
    font-weight: 600;
    margin: 0.5em 0 0.25em;
    color: #e2e8f0;
}
.preview-richtext :deep(ul) {
    list-style: disc;
    padding-left: 1.25em;
    margin: 0.25em 0;
}
.preview-richtext :deep(ol) {
    list-style: decimal;
    padding-left: 1.25em;
    margin: 0.25em 0;
}
.preview-richtext :deep(p) {
    margin: 0.15em 0;
}
.preview-richtext :deep(strong) {
    color: #f1f5f9;
}
</style>
