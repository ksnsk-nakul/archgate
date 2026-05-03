<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type Career = { title: string; location?: string; type?: string; description?: string };
type LandingData = { careers: string };

const page = usePage<{ landing: LandingData; appDetails: { name: string } }>();
const appName = computed(() => page.props.appDetails?.name ?? 'FluxHaven');
const careers = computed<Career[]>(() => {
    try { return page.props.landing?.careers ? JSON.parse(page.props.landing.careers) : []; } catch { return []; }
});
</script>

<template>
    <Head :title="`Careers — ${appName}`" />

    <!-- Hero -->
    <section class="py-20 px-4 md:px-6 text-center" style="background: linear-gradient(135deg, #051424 0%, #0a1929 60%, #051424 100%);">
        <div class="max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 bg-[var(--primary)]/10 border border-[var(--primary)]/20 text-[var(--primary)] text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-6">Careers</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight" style="font-family: Manrope, sans-serif;">Join our team</h1>
            <p class="text-lg text-slate-400">Build something that matters. We're looking for passionate people.</p>
        </div>
    </section>

    <!-- Job listings -->
    <section class="py-16 px-4 md:px-6 bg-[#051424]">
        <div class="max-w-4xl mx-auto">
            <!-- Empty state -->
            <div v-if="careers.length === 0" class="text-center py-20">
                <div class="size-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto mb-5">
                    <svg class="size-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2" style="font-family: Manrope, sans-serif;">No open positions right now</h3>
                <p class="text-slate-500 text-sm">Check back soon — we're growing fast.</p>
            </div>

            <!-- Cards -->
            <div v-else class="flex flex-col gap-4">
                <div
                    v-for="(job, i) in careers"
                    :key="i"
                    class="group rounded-2xl border border-slate-800 bg-[#0d1c2d] p-6 hover:border-[var(--primary)]/30 transition-colors"
                >
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                        <h3 class="text-lg font-bold text-white min-w-0" style="font-family: Manrope, sans-serif;">{{ job.title }}</h3>
                        <div class="flex items-center gap-2 shrink-0">
                            <span v-if="job.type" class="text-xs font-semibold text-[var(--primary)] bg-[var(--primary)]/10 border border-[var(--primary)]/20 px-2.5 py-1 rounded-full">{{ job.type }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                        <span v-if="job.location" class="flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            {{ job.location }}
                        </span>
                    </div>
                    <p v-if="job.description" class="text-sm text-slate-400 leading-relaxed mb-5">{{ job.description }}</p>
                    <a href="/contact" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] hover:text-orange-400 transition-colors">
                        Apply for this role
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
