<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { update } from '@/routes/admin/settings/app';

defineProps<{
    settings: {
        app_name: string;
        logo_url?: string | null;
        favicon_url?: string | null;
        primary_color?: string | null;
    };
}>();
</script>

<template>
    <Head title="App settings" />

    <div class="flex flex-col min-h-full bg-[#051424] text-[#d4e4fa]" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-800">
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">Admin</p>
                <h1 class="text-xl font-bold text-white" style="font-family: Manrope, sans-serif;">App Settings</h1>
            </div>
        </div>

        <div class="px-6 py-6 max-w-2xl">
            <div class="rounded-xl border border-slate-800 bg-[#0d1c2d] overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800">
                    <h2 class="text-sm font-bold text-white" style="font-family: Manrope, sans-serif;">App details</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Public app identity displayed across the platform.</p>
                </div>

                <Form
                    v-bind="update.form()"
                    enctype="multipart/form-data"
                    class="px-6 py-6 flex flex-col gap-6"
                    v-slot="{ errors, processing }"
                >
                    <!-- App name -->
                    <div class="flex flex-col gap-2">
                        <label for="app_name" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">App name</label>
                        <input
                            id="app_name"
                            name="app_name"
                            :value="settings.app_name"
                            required
                            class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors"
                        />
                        <InputError :message="errors.app_name" />
                    </div>

                    <!-- Primary colour -->
                    <div class="flex flex-col gap-2">
                        <label for="primary_color" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Brand colour</label>
                        <div class="flex items-center gap-3">
                            <div class="relative size-10 rounded-lg overflow-hidden border border-slate-700 shrink-0 cursor-pointer">
                                <input
                                    id="primary_color"
                                    name="primary_color"
                                    type="color"
                                    :value="settings.primary_color ?? `var(--primary)`"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    oninput="this.previousElementSibling.style.background=this.value; document.getElementById('primary_color_hex').value=this.value;"
                                />
                                <div class="size-full rounded-lg" :style="{ background: settings.primary_color ?? `var(--primary)` }"></div>
                            </div>
                            <input
                                id="primary_color_hex"
                                type="text"
                                :value="settings.primary_color ?? `var(--primary)`"
                                maxlength="7"
                                placeholder="#f7600d"
                                class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 font-mono w-32 focus:outline-none focus:border-[var(--primary)] transition-colors"
                                oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value)){document.getElementById('primary_color').value=this.value; this.previousElementSibling.querySelector('div').style.background=this.value;}"
                            />
                            <p class="text-xs text-slate-500">Used for buttons, active states, and accents site-wide.</p>
                        </div>
                        <InputError :message="errors.primary_color" />
                    </div>

                    <!-- Logo -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Logo</label>
                        <div v-if="settings.logo_url" class="flex items-center gap-3 rounded-lg border border-slate-700 bg-slate-900/50 p-3">
                            <img :src="settings.logo_url" alt="Current logo" class="h-10 max-w-48 object-contain" />
                            <span class="text-xs text-slate-500">Current logo</span>
                        </div>
                        <input
                            id="logo"
                            name="logo"
                            type="file"
                            accept="image/*"
                            class="text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-300 hover:file:bg-slate-700 file:cursor-pointer cursor-pointer"
                        />
                        <p class="text-xs text-slate-600">PNG, JPG, SVG or WebP — max 2 MB</p>
                        <InputError :message="errors.logo" />
                    </div>

                    <!-- Favicon -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Favicon</label>
                        <div v-if="settings.favicon_url" class="flex items-center gap-3 rounded-lg border border-slate-700 bg-slate-900/50 p-3">
                            <img :src="settings.favicon_url" alt="Current favicon" class="size-8 object-contain" />
                            <span class="text-xs text-slate-500">Current favicon</span>
                        </div>
                        <input
                            id="favicon"
                            name="favicon"
                            type="file"
                            accept=".ico,image/*"
                            class="text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-300 hover:file:bg-slate-700 file:cursor-pointer cursor-pointer"
                        />
                        <p class="text-xs text-slate-600">ICO, PNG, JPG — max 1 MB</p>
                        <InputError :message="errors.favicon" />
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                        >
                            <svg v-if="processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Save app settings
                        </button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>
