import { createInertiaApp, router } from '@inertiajs/vue3';

import { createPinia } from 'pinia';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

/** Apply the brand primary colour from shared Inertia props to :root CSS variable */
function applyPrimaryColor(color: string | null | undefined): void {
    if (!color) { return; }
    document.documentElement.style.setProperty('--primary', color);
    document.documentElement.style.setProperty(
        '--primary-hover',
        `color-mix(in srgb, ${color} 85%, black)`,
    );
    document.documentElement.style.setProperty(
        '--primary-dim',
        `color-mix(in srgb, ${color} 10%, transparent)`,
    );
    document.documentElement.style.setProperty(
        '--primary-border',
        `color-mix(in srgb, ${color} 20%, transparent)`,
    );
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    withApp(app) {
        app.use(createPinia());
    },
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// Re-apply primary colour on every Inertia navigation so the variable stays
// in sync if the admin changes it and the page refreshes.
router.on('navigate', (event) => {
    const color = (event.detail.page.props as Record<string, unknown> & { appDetails?: { primaryColor?: string } })
        .appDetails?.primaryColor;
    applyPrimaryColor(color);
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
s

router.on('navigate', (event) => {
    const color = (event.detail.page.props as Record<string, unknown> & { appDetails?: { primaryColor?: string } })
        .appDetails?.primaryColor;
    applyPrimaryColor(color);
});
