<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { useTheme } from '@/composables/useTheme';
import { update } from '@/routes/admin/settings/app';

// Preview swatch selection also live-previews the theme in real time
function useImagePreview(initial: string | null | undefined) {
    const preview = ref<string | null>(initial ?? null);
    function onFile(e: Event): void {
        const file = (e.target as HTMLInputElement).files?.[0];
        if (!file) { return; }
        const reader = new FileReader();
        reader.onload = (ev) => { preview.value = ev.target?.result as string; };
        reader.readAsDataURL(file);
    }
    return { preview, onFile };
}

const props = defineProps<{
    settings: {
        app_name: string;
        logo_url?: string | null;
        favicon_url?: string | null;
        primary_color?: string | null;
    };
}>();

const themes = [
    { name: 'Stitch Orange', primary: '#EC5800', label: 'Default' },
    { name: 'Cobalt Blue',   primary: '#2563EB' },
    { name: 'Emerald',       primary: '#059669' },
    { name: 'Violet',        primary: '#7C3AED' },
    { name: 'Rose',          primary: '#E11D48' },
    { name: 'Amber',         primary: '#D97706' },
    { name: 'Cyan',          primary: '#0891B2' },
    { name: 'Pink',          primary: '#DB2777' },
    { name: 'Indigo',        primary: '#4F46E5' },
    { name: 'Teal',          primary: '#0D9488' },
    { name: 'Slate',         primary: '#475569' },
    { name: 'Fuchsia',       primary: '#A21CAF' },
];

const brandColor = ref(props.settings.primary_color ?? '#EC5800');

const { preview: logoPreview, onFile: onLogoFile } = useImagePreview(props.settings.logo_url);
const { preview: faviconPreview, onFile: onFaviconFile } = useImagePreview(props.settings.favicon_url);

// Apply theme reactively (handles initial load + post-save prop updates)
useTheme();

// Keep local state in sync when props refresh after a successful save/redirect
watch(() => props.settings.primary_color, (val) => {
    brandColor.value = val ?? '#EC5800';
});
watch(() => props.settings.logo_url, (val) => {
    logoPreview.value = val ?? null;
});
watch(() => props.settings.favicon_url, (val) => {
    faviconPreview.value = val ?? null;
});

function selectTheme(color: string): void {
    brandColor.value = color;
    // Immediately preview the selected color on :root so the user sees it
    document.documentElement.style.setProperty('--primary', color);
    document.documentElement.style.setProperty('--primary-hover', `color-mix(in srgb, ${color} 85%, black)`);
    document.documentElement.style.setProperty('--primary-dim', `color-mix(in srgb, ${color} 12%, transparent)`);
    document.documentElement.style.setProperty('--ring', color);
    document.documentElement.style.setProperty('--sidebar-primary', color);
    document.documentElement.style.setProperty('--sidebar-accent-foreground', color);
}
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
                        <label
                            for="logo"
                            class="relative flex items-center justify-center w-full h-28 rounded-xl border-2 border-dashed border-app bg-app-elevated/40 hover:bg-app-elevated/70 hover:border-[var(--primary)] transition-all cursor-pointer group overflow-hidden"
                        >
                            <img
                                v-if="logoPreview"
                                :src="logoPreview"
                                alt="Logo preview"
                                class="max-h-20 max-w-full object-contain"
                            />
                            <div v-else class="flex flex-col items-center gap-2 text-app-muted group-hover:text-app transition-colors">
                                <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-medium">Click to upload logo</span>
                            </div>
                            <div v-if="logoPreview" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <span class="text-xs font-semibold text-white">Change image</span>
                            </div>
                            <input id="logo" name="logo" type="file" accept="image/*" class="sr-only" @change="onLogoFile" />
                        </label>
                        <p class="text-xs text-app-muted">PNG, JPG, SVG or WebP — max 2 MB</p>
                        <InputError :message="errors.logo" />
                    </div>

                    <!-- Favicon -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-app-muted uppercase tracking-wider">Favicon</label>
                        <label
                            for="favicon"
                            class="relative flex items-center justify-center w-24 h-24 rounded-xl border-2 border-dashed border-app bg-app-elevated/40 hover:bg-app-elevated/70 hover:border-[var(--primary)] transition-all cursor-pointer group overflow-hidden"
                        >
                            <img
                                v-if="faviconPreview"
                                :src="faviconPreview"
                                alt="Favicon preview"
                                class="size-12 object-contain"
                            />
                            <div v-else class="flex flex-col items-center gap-1.5 text-app-muted group-hover:text-app transition-colors">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-[10px] font-medium text-center leading-tight">Upload favicon</span>
                            </div>
                            <div v-if="faviconPreview" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <span class="text-[10px] font-semibold text-white">Change</span>
                            </div>
                            <input id="favicon" name="favicon" type="file" accept=".ico,image/*" class="sr-only" @change="onFaviconFile" />
                        </label>
                        <p class="text-xs text-app-muted">ICO, PNG, JPG — max 1 MB</p>
                        <InputError :message="errors.favicon" />
                    </div>

                    <!-- Brand colour / theme -->
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-app-muted uppercase tracking-wider">Theme colour</label>
                            <!-- Hidden field carries the actual value -->
                            <input type="hidden" name="primary_color" :value="brandColor" />
                            <!-- Active preview pill -->
                            <div
                                class="flex items-center gap-2 px-3 py-1 rounded-full border text-xs font-semibold"
                                :style="{ background: brandColor + '1a', color: brandColor, borderColor: brandColor + '40' }"
                            >
                                <div class="size-2.5 rounded-full" :style="{ background: brandColor }" />
                                {{ themes.find(t => t.primary === brandColor)?.name ?? 'Custom' }}
                            </div>
                        </div>

                        <!-- Preset swatches grid -->
                        <div class="grid grid-cols-6 gap-2">
                            <button
                                v-for="theme in themes"
                                :key="theme.primary"
                                type="button"
                                :title="theme.name"
                                @click="selectTheme(theme.primary)"
                                class="relative group flex flex-col items-center gap-1.5"
                            >
                                <span
                                    class="block size-9 rounded-xl border-2 transition-all"
                                    :style="{ background: theme.primary, borderColor: brandColor === theme.primary ? theme.primary : 'transparent' }"
                                    :class="brandColor === theme.primary ? 'scale-110 shadow-lg' : 'opacity-75 hover:opacity-100 hover:scale-105'"
                                >
                                    <span v-if="brandColor === theme.primary" class="flex items-center justify-center h-full">
                                        <svg class="size-4 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </span>
                                <span class="text-[10px] text-app-muted leading-tight text-center w-full truncate">{{ theme.label ?? theme.name.split(' ')[0] }}</span>
                            </button>
                        </div>

                        <p class="text-xs text-app-muted">Applied to buttons, active states, and highlights across the app.</p>
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
