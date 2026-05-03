<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type LandingData = { contact_email: string; contact_phone: string; contact_address: string };
const page = usePage<{ landing: LandingData; appDetails: { name: string } }>();
const appName = computed(() => page.props.appDetails?.name ?? 'FluxHaven');
const contactEmail   = computed(() => page.props.landing?.contact_email ?? '');
const contactPhone   = computed(() => page.props.landing?.contact_phone ?? '');
const contactAddress = computed(() => page.props.landing?.contact_address ?? '');

const form = ref({ name: '', email: '', phone: '', interest: 'learn', message: '', website: '' });
const submitted = ref(false);
const submitting = ref(false);
const errors = ref<Record<string, string>>({});

function submit() {
    errors.value = {};
    if (!form.value.name.trim()) { errors.value.name = 'Name is required.'; }
    if (!form.value.email.trim()) { errors.value.email = 'Email is required.'; }
    if (Object.keys(errors.value).length) { return; }
    if (form.value.website) { return; } // honeypot

    submitting.value = true;
    router.post('/landing/contact', {
        name: form.value.name,
        email: form.value.email,
        phone: form.value.phone,
        interest: form.value.interest,
        message: form.value.message,
        website: form.value.website,
    }, {
        onSuccess: () => { submitted.value = true; },
        onError: (e) => { errors.value = e; },
        onFinish: () => { submitting.value = false; },
    });
}
</script>

<template>
    <Head :title="`Contact — ${appName}`" />

    <!-- Hero -->
    <section class="py-20 px-4 md:px-6 text-center" style="background: linear-gradient(135deg, #051424 0%, #0a1929 60%, #051424 100%);">
        <div class="max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 bg-[var(--primary)]/10 border border-[var(--primary)]/20 text-[var(--primary)] text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-6">Contact</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight" style="font-family: Manrope, sans-serif;">Get in touch</h1>
            <p class="text-lg text-slate-400">We'd love to hear from you. Tell us what you're looking for.</p>
        </div>
    </section>

    <!-- Two-column -->
    <section class="py-16 px-4 md:px-6 bg-[#051424]">
        <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6 md:gap-12">

            <!-- Contact info -->
            <div class="flex flex-col gap-6">
                <h2 class="text-xl font-bold text-white" style="font-family: Manrope, sans-serif;">Contact information</h2>
                <div v-if="contactEmail" class="flex items-start gap-4">
                    <div class="size-10 rounded-xl bg-[var(--primary)]/10 flex items-center justify-center shrink-0">
                        <svg class="size-5 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Email</p>
                        <a :href="`mailto:${contactEmail}`" class="text-sm text-slate-300 hover:text-[var(--primary)] transition-colors">{{ contactEmail }}</a>
                    </div>
                </div>
                <div v-if="contactPhone" class="flex items-start gap-4">
                    <div class="size-10 rounded-xl bg-[var(--primary)]/10 flex items-center justify-center shrink-0">
                        <svg class="size-5 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Phone</p>
                        <p class="text-sm text-slate-300">{{ contactPhone }}</p>
                    </div>
                </div>
                <div v-if="contactAddress" class="flex items-start gap-4">
                    <div class="size-10 rounded-xl bg-[var(--primary)]/10 flex items-center justify-center shrink-0">
                        <svg class="size-5 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Address</p>
                        <p class="text-sm text-slate-300 whitespace-pre-line">{{ contactAddress }}</p>
                    </div>
                </div>
                <div v-if="!contactEmail && !contactPhone && !contactAddress" class="text-slate-500 text-sm">Contact details coming soon.</div>
            </div>

            <!-- Lead form -->
            <div class="rounded-2xl border border-slate-800 bg-[#0d1c2d] p-6">
                <!-- Thank-you state -->
                <div v-if="submitted" class="flex flex-col items-center justify-center py-10 text-center gap-4">
                    <div class="size-14 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                        <svg class="size-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-white" style="font-family: Manrope, sans-serif;">Message received!</h3>
                    <p class="text-sm text-slate-400">Thanks for reaching out. We'll get back to you shortly.</p>
                </div>

                <!-- Form -->
                <form v-else @submit.prevent="submit" class="flex flex-col gap-4">
                    <h3 class="text-sm font-bold text-white mb-1" style="font-family: Manrope, sans-serif;">Send us a message</h3>
                    <!-- Honeypot -->
                    <input v-model="form.website" type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" />

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Name *</label>
                        <input v-model="form.name" type="text" placeholder="Your name" class="bg-slate-900 border rounded-lg px-3 py-2.5 text-sm text-slate-100 placeholder-slate-600 outline-none transition-colors" :class="errors.name ? 'border-red-500' : 'border-slate-700 focus:border-[var(--primary)]'" />
                        <p v-if="errors.name" class="text-xs text-red-400">{{ errors.name }}</p>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Email *</label>
                        <input v-model="form.email" type="email" placeholder="you@example.com" class="bg-slate-900 border rounded-lg px-3 py-2.5 text-sm text-slate-100 placeholder-slate-600 outline-none transition-colors" :class="errors.email ? 'border-red-500' : 'border-slate-700 focus:border-[var(--primary)]'" />
                        <p v-if="errors.email" class="text-xs text-red-400">{{ errors.email }}</p>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Phone</label>
                        <input v-model="form.phone" type="tel" placeholder="+1 (555) 000-0000" class="bg-slate-900 border border-slate-700 focus:border-[var(--primary)] rounded-lg px-3 py-2.5 text-sm text-slate-100 placeholder-slate-600 outline-none transition-colors" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">I'm interested in</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input v-model="form.interest" type="radio" value="learn" class="accent-[var(--primary)]" />
                                <span class="text-sm text-slate-300">Learning</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input v-model="form.interest" type="radio" value="buy" class="accent-[var(--primary)]" />
                                <span class="text-sm text-slate-300">Purchasing</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Message</label>
                        <textarea v-model="form.message" rows="3" placeholder="Tell us more..." class="bg-slate-900 border border-slate-700 focus:border-[var(--primary)] rounded-lg px-3 py-2.5 text-sm text-slate-100 placeholder-slate-600 outline-none resize-none transition-colors" />
                    </div>
                    <button type="submit" :disabled="submitting" class="flex items-center justify-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors mt-1">
                        <svg v-if="submitting" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Send message
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
