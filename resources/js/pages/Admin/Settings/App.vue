<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { update } from '@/routes/admin/settings/app';

const props = defineProps<{
    settings: {
        app_name: string;
        logo_url?: string | null;
        favicon_url?: string | null;
        primary_color?: string | null;
    };
}>();

const brandColor = ref(props.settings.primary_color ?? '#f7600d');
</script>

<template>
    <Head title="App settings" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Admin</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">App Settings</h1>
            </div>
        </div>

        <div class="px-6 py-6 max-w-2xl">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">App details</h2>
                    <p class="text-xs text-app-muted mt-0.5">Public app identity displayed across the platform.</p>
                </div>

                <Form
                    v-bind="update.form()"
                    enctype="multipart/form-data"
                    class="px-6 py-6 flex flex-col gap-6"
                    v-slot="{ errors, processing }"
                >
                    <!-- App name -->
                    <div class="flex flex-col gap-2">
                        <label for="app_name" class="text-xs font-semibold text-app-muted uppercase tracking-wider">App name</label>
                        <input
                            id="app_name"
                            name="app_name"
                            :value="settings.app_name"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                        />
                        <InputError :message="errors.app_name" />
                    </div>

                    <!-- Logo -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-app-muted uppercase tracking-wider">Logo</label>
                        <div v-if="settings.logo_url" class="flex items-center gap-3 rounded-lg border border-app bg-app-elevated/50 p-3">
                            <img :src="settings.logo_url" alt="Current logo" class="h-10 max-w-48 object-contain" />
                            <span class="text-xs text-app-muted">Current logo</span>
                        </div>
                        <input
                            id="logo"
                            name="logo"
                            type="file"
                            accept="image/*"
                            class="text-sm text-app-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-app-elevated file:text-app-muted hover:file:bg-slate-700 file:cursor-pointer cursor-pointer"
                        />
                        <p class="text-xs text-app-muted">PNG, JPG, SVG or WebP — max 2 MB</p>
                        <InputError :message="errors.logo" />
                    </div>

                    <!-- Favicon -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-app-muted uppercase tracking-wider">Favicon</label>
                        <div v-if="settings.favicon_url" class="flex items-center gap-3 rounded-lg border border-app bg-app-elevated/50 p-3">
                            <img :src="settings.favicon_url" alt="Current favicon" class="size-8 object-contain" />
                            <span class="text-xs text-app-muted">Current favicon</span>
                        </div>
                        <input
                            id="favicon"
                            name="favicon"
                            type="file"
                            accept=".ico,image/*"
                            class="text-sm text-app-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-app-elevated file:text-app-muted hover:file:bg-slate-700 file:cursor-pointer cursor-pointer"
                        />
                        <p class="text-xs text-app-muted">ICO, PNG, JPG — max 1 MB</p>
                        <InputError :message="errors.favicon" />
                    </div>

                    <!-- Brand colour -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-app-muted uppercase tracking-wider">Brand colour</label>
                        <div class="flex items-center gap-3">
                            <!-- Native colour picker -->
                            <div class="relative shrink-0">
                                <input
                                    v-model="brandColor"
                                    type="color"
                                    name="primary_color"
                                    class="size-10 rounded-lg border-2 border-app cursor-pointer bg-transparent p-0.5"
                                    title="Pick brand colour"
                                />
                            </div>
                            <!-- Hex input -->
                            <input
                                v-model="brandColor"
                                name="primary_color"
                                type="text"
                                maxlength="7"
                                placeholder="#f7600d"
                                class="input-app w-32 rounded-lg px-3 py-2 text-sm font-mono border transition-colors"
                            />
                            <!-- Live swatch -->
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-app text-xs font-semibold"
                                :style="{ background: brandColor + '1a', color: brandColor, borderColor: brandColor + '33' }"
                            >
                                <div class="size-3 rounded-full" :style="{ background: brandColor }" />
                                Active colour
                            </div>
                        </div>
                        <p class="text-xs text-app-muted">Used for buttons, active states, and highlights across the app.</p>
                        <InputError :message="errors.primary_color" />
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
