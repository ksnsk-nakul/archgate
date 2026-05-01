<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard, login, register } from '@/routes';

defineProps<{ canRegister: boolean }>();

type NavLink = { label: string; href: string };
type Service = { icon: string; title: string; body: string };
type Career  = { title: string; location?: string; type?: string; description?: string };
type LandingData = {
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
};

const page    = usePage<{ landing: LandingData; auth: { user: unknown } }>();
const landing = computed(() => page.props.landing);

const parseJson = <T>(val: string | null | undefined, fallback: T): T => {
    try { return val ? JSON.parse(val) : fallback; } catch { return fallback; }
};

const navLinks   = computed<NavLink[]>(() => parseJson(landing.value?.nav_links, []));
const services   = computed<Service[]>(() => parseJson(landing.value?.services, []));
const careers    = computed<Career[]> (() => parseJson(landing.value?.careers, []));
const heroTitle    = computed(() => landing.value?.hero_title    ?? 'Build, Learn & Grow');
const heroSubtitle = computed(() => landing.value?.hero_subtitle ?? 'The all-in-one platform for teams, learners, and businesses.');
const ctaLabel     = computed(() => landing.value?.cta_label     ?? 'Get started free');
const aboutText    = computed(() => landing.value?.about_text    ?? '');
const footerText   = computed(() => landing.value?.footer_text   ?? `© ${new Date().getFullYear()} FluxHaven. All rights reserved.`);
const contactEmail   = computed(() => landing.value?.contact_email   ?? '');
const contactPhone   = computed(() => landing.value?.contact_phone   ?? '');
const contactAddress = computed(() => landing.value?.contact_address ?? '');

const serviceIcons: Record<string, string> = {
    briefcase:  'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    users:      'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    book:       'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    graduation: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
    default:    'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
};
</script>

<template>
    <Head :title="`${heroTitle} — FluxHaven`">
        <meta name="description" :content="heroSubtitle" />
    </Head>

    <div class="min-h-screen bg-[#051424] text-[#d4e4fa]" style="font-family: Inter, sans-serif;">

        <!-- Nav -->
        <header class="sticky top-0 z-50 border-b border-slate-800 bg-[#051424]/95 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#f7600d]">
                        <svg class="size-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white" style="font-family: Manrope, sans-serif;">FluxHaven</span>
                </div>
                <nav class="hidden md:flex items-center gap-1">
                    <a
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="px-3 py-1.5 text-sm text-slate-400 hover:text-white rounded-lg hover:bg-slate-800/50 transition-colors"
                    >
                        {{ link.label }}
                    </a>
                </nav>
                <div class="flex items-center gap-2">
                    <Link v-if="$page.props.auth.user" :href="dashboard()" class="rounded-lg bg-[#f7600d] px-4 py-1.5 text-sm font-semibold text-white hover:bg-orange-600 transition-colors">
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="login()" class="px-4 py-1.5 text-sm text-slate-400 hover:text-white transition-colors">Log in</Link>
                        <Link v-if="canRegister" :href="register()" class="rounded-lg bg-[#f7600d] px-4 py-1.5 text-sm font-semibold text-white hover:bg-orange-600 transition-colors">
                            {{ ctaLabel }}
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="mx-auto max-w-6xl px-4 py-24 text-center">
            <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-[#f7600d]/30 bg-[#f7600d]/10 px-4 py-1.5 text-xs font-semibold text-[#f7600d]">
                <span class="h-1.5 w-1.5 rounded-full bg-[#f7600d]" />
                Now in version 1 — ready for production
            </div>
            <!-- eslint-disable-next-line vue/no-v-html -->
            <h1 class="mb-6 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-white" style="font-family: Manrope, sans-serif;" v-html="heroTitle.replace(/\n/g, '<br>')" />
            <!-- eslint-disable-next-line vue/no-v-html -->
            <p class="mx-auto mb-10 max-w-2xl text-lg text-slate-400" v-html="heroSubtitle" />
            <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
                <Link v-if="canRegister && !$page.props.auth.user" :href="register()" class="inline-flex items-center gap-2 rounded-xl bg-[#f7600d] px-7 py-3.5 text-sm font-semibold text-white shadow-lg hover:bg-orange-600 transition-colors">
                    {{ ctaLabel }}
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </Link>
                <Link v-if="$page.props.auth.user" :href="dashboard()" class="inline-flex items-center gap-2 rounded-xl bg-[#f7600d] px-7 py-3.5 text-sm font-semibold text-white shadow-lg hover:bg-orange-600 transition-colors">
                    Go to Dashboard
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </Link>
                <Link v-if="!$page.props.auth.user" :href="login()" class="rounded-xl border border-slate-700 px-7 py-3.5 text-sm font-semibold text-slate-300 hover:border-slate-500 hover:text-white transition-colors">
                    Log in to your account
                </Link>
            </div>
        </section>

        <!-- Services -->
        <section v-if="services.length" class="border-t border-slate-800 bg-[#0a1929] py-20">
            <div class="mx-auto max-w-6xl px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-3 text-2xl font-bold text-white sm:text-3xl" style="font-family: Manrope, sans-serif;">Everything in one place</h2>
                    <p class="text-slate-400 text-sm">Integrated modules that replace multiple SaaS tools.</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="svc in services" :key="svc.title" class="rounded-xl border border-slate-800 bg-[#0d1c2d] p-5 hover:border-[#f7600d]/30 transition-colors">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-[#f7600d]/10 border border-[#f7600d]/20">
                            <svg class="size-5 text-[#f7600d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="serviceIcons[svc.icon] ?? serviceIcons.default" />
                            </svg>
                        </div>
                        <h3 class="mb-1.5 text-sm font-bold text-white">{{ svc.title }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ svc.body }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About -->
        <section v-if="aboutText" class="py-20">
            <div class="mx-auto max-w-3xl px-4 text-center">
                <h2 class="mb-6 text-2xl font-bold text-white sm:text-3xl" style="font-family: Manrope, sans-serif;">About FluxHaven</h2>
                <!-- eslint-disable-next-line vue/no-v-html -->
                <div class="text-slate-400 leading-relaxed" v-html="aboutText" />
            </div>
        </section>

        <!-- Careers -->
        <section v-if="careers.length" class="border-t border-slate-800 bg-[#0a1929] py-20">
            <div class="mx-auto max-w-4xl px-4">
                <div class="mb-10 text-center">
                    <h2 class="mb-3 text-2xl font-bold text-white sm:text-3xl" style="font-family: Manrope, sans-serif;">Open Roles</h2>
                    <p class="text-slate-400 text-sm">Join us and help build the future of team productivity.</p>
                </div>
                <div class="flex flex-col gap-4">
                    <div v-for="job in careers" :key="job.title" class="rounded-xl border border-slate-800 bg-[#0d1c2d] px-6 py-5 flex items-start justify-between gap-4">
                        <div>
                            <p class="font-bold text-white text-sm">{{ job.title }}</p>
                            <p v-if="job.description" class="text-xs text-slate-500 mt-1 leading-relaxed">{{ job.description }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span v-if="job.location" class="text-xs text-slate-500 bg-slate-800 px-2.5 py-1 rounded-full">{{ job.location }}</span>
                            <span v-if="job.type" class="text-xs text-[#f7600d] bg-[#f7600d]/10 border border-[#f7600d]/20 px-2.5 py-1 rounded-full">{{ job.type }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact -->
        <section v-if="contactEmail || contactPhone || contactAddress" class="py-20 border-t border-slate-800">
            <div class="mx-auto max-w-2xl px-4 text-center">
                <h2 class="mb-6 text-2xl font-bold text-white sm:text-3xl" style="font-family: Manrope, sans-serif;">Get in touch</h2>
                <div class="flex flex-col gap-3 items-center">
                    <a v-if="contactEmail" :href="`mailto:${contactEmail}`" class="flex items-center gap-2 text-slate-400 hover:text-[#f7600d] transition-colors text-sm">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ contactEmail }}
                    </a>
                    <a v-if="contactPhone" :href="`tel:${contactPhone}`" class="flex items-center gap-2 text-slate-400 hover:text-[#f7600d] transition-colors text-sm">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ contactPhone }}
                    </a>
                    <p v-if="contactAddress" class="text-slate-500 text-sm mt-1">{{ contactAddress }}</p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="border-t border-slate-800 bg-[#f7600d] py-16 text-white">
            <div class="mx-auto max-w-3xl px-4 text-center">
                <h2 class="mb-3 text-2xl font-bold sm:text-3xl" style="font-family: Manrope, sans-serif;">Ready to bring it all together?</h2>
                <p class="mb-8 opacity-80 text-sm">Create your account in seconds. No credit card required.</p>
                <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <Link v-if="canRegister && !$page.props.auth.user" :href="register()" class="rounded-xl bg-white px-7 py-3 text-sm font-bold text-[#f7600d] hover:bg-slate-100 transition-colors">
                        {{ ctaLabel }}
                    </Link>
                    <Link v-if="$page.props.auth.user" :href="dashboard()" class="rounded-xl bg-white px-7 py-3 text-sm font-bold text-[#f7600d] hover:bg-slate-100 transition-colors">
                        Go to Dashboard
                    </Link>
                    <Link v-if="!$page.props.auth.user" :href="login()" class="rounded-xl border border-white/30 px-7 py-3 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                        Log in
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-800 py-6">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 text-xs text-slate-600 sm:flex-row">
                <!-- eslint-disable-next-line vue/no-v-html -->
                <span v-html="footerText" />
                <span>Built with Laravel + Inertia + Vue</span>
                <span>V1.0.0</span>
            </div>
        </footer>
    </div>
</template>
