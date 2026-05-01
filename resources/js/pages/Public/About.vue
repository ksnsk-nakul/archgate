<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type LandingData = { about_text: string };
const page = usePage<{ landing: LandingData; appDetails: { name: string } }>();
const appName   = computed(() => page.props.appDetails?.name ?? 'FluxHaven');
const aboutText = computed(() => page.props.landing?.about_text ?? '');
</script>

<template>
    <Head :title="`About — ${appName}`" />

    <!-- Hero -->
    <section class="py-20 px-6 text-center" style="background: linear-gradient(135deg, #051424 0%, #0a1929 60%, #051424 100%);">
        <div class="max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 bg-[#f7600d]/10 border border-[#f7600d]/20 text-[#f7600d] text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-6">About Us</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight" style="font-family: Manrope, sans-serif;">Who we are</h1>
        </div>
    </section>

    <!-- About content -->
    <section class="py-16 px-6 bg-[#051424]">
        <div class="max-w-3xl mx-auto">
            <div
                v-if="aboutText"
                class="about-prose text-slate-300 leading-relaxed text-base"
                v-html="aboutText"
            />
            <p v-else class="text-slate-400 text-base leading-relaxed">
                {{ appName }} is built for modern teams who want to learn and ship faster. We bring together project management, CRM, a digital library, and structured learning paths into a single cohesive platform.
            </p>
        </div>
    </section>
</template>

<style scoped>
.about-prose :deep(h2) { font-size: 1.4em; font-weight: 700; color: #fff; margin: 1.2em 0 0.5em; font-family: Manrope, sans-serif; }
.about-prose :deep(h3) { font-size: 1.1em; font-weight: 600; color: #e2e8f0; margin: 1em 0 0.4em; }
.about-prose :deep(p)  { margin: 0.6em 0; }
.about-prose :deep(ul) { list-style: disc; padding-left: 1.5em; margin: 0.5em 0; }
.about-prose :deep(ol) { list-style: decimal; padding-left: 1.5em; margin: 0.5em 0; }
.about-prose :deep(li) { margin: 0.25em 0; }
.about-prose :deep(strong) { color: #f1f5f9; font-weight: 600; }
</style>
