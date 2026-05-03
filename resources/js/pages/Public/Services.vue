<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type Service = { icon: string; title: string; body: string };
type LandingData = { services: string };

const page = usePage<{ landing: LandingData; appDetails: { name: string } }>();
const appName = computed(() => page.props.appDetails?.name ?? 'FluxHaven');
const services = computed<Service[]>(() => {
    try { return page.props.landing?.services ? JSON.parse(page.props.landing.services) : []; } catch { return []; }
});

const iconPaths: Record<string, string> = {
    briefcase:  'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    users:      'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    book:       'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    graduation: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
    chart:      'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    default:    'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
};
</script>

<template>
    <Head :title="`Services — ${appName}`" />

    <!-- Hero -->
    <section class="py-20 px-4 md:px-6 text-center" style="background: linear-gradient(135deg, #051424 0%, #0a1929 60%, #051424 100%);">
        <div class="max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 bg-[var(--primary)]/10 border border-[var(--primary)]/20 text-[var(--primary)] text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-6">Our Services</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight" style="font-family: Manrope, sans-serif;">Everything your team needs</h1>
            <p class="text-lg text-slate-400 leading-relaxed">A complete platform for managing projects, leads, learning, and more — all in one place.</p>
        </div>
    </section>

    <!-- Services grid -->
    <section class="py-16 px-4 md:px-6 bg-[#051424]">
        <div class="max-w-6xl mx-auto">
            <div v-if="services.length === 0" class="text-center py-16 text-slate-500">No services configured yet.</div>
            <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="(svc, i) in services"
                    :key="i"
                    class="group rounded-2xl border border-slate-800 bg-[#0d1c2d] p-6 hover:border-[var(--primary)]/30 transition-colors"
                >
                    <div class="size-12 rounded-xl bg-[var(--primary)]/10 border border-[var(--primary)]/20 flex items-center justify-center mb-5 group-hover:bg-[var(--primary)]/15 transition-colors">
                        <svg class="size-6 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="iconPaths[svc.icon] ?? iconPaths.default" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2" style="font-family: Manrope, sans-serif;">{{ svc.title }}</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">{{ svc.body }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 px-4 md:px-6 bg-[#0a1929] text-center border-t border-slate-800">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-3xl font-extrabold text-white mb-4" style="font-family: Manrope, sans-serif;">Ready to get started?</h2>
            <p class="text-slate-400 mb-8">Join teams already using {{ appName }} to work smarter.</p>
            <a href="/register" class="inline-flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold px-8 py-3.5 rounded-xl text-base transition-colors shadow-lg">
                Start for free
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
            </a>
        </div>
    </section>
</template>
