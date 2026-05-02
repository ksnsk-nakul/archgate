import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

type AppDetails = {
    name: string;
    logoUrl?: string | null;
    faviconUrl?: string | null;
    primaryColor?: string | null;
};

/**
 * Applies the per-tenant primary color (from Inertia shared props) to
 * CSS custom properties on :root — immediately and reactively whenever
 * the saved color changes (no full page reload required).
 *
 * Also derives secondary / accent / ring tokens so all shadcn components
 * and card elements flex with the selected theme colour.
 */
export function useTheme(): void {
    const page = usePage<{ appDetails: AppDetails }>();
    const primaryColor = computed(() => page.props.appDetails?.primaryColor ?? '#EC5800');

    function applyTheme(color: string): void {
        const root = document.documentElement;
        root.style.setProperty('--primary', color);
        root.style.setProperty('--primary-hover', `color-mix(in srgb, ${color} 85%, black)`);
        root.style.setProperty('--primary-dim', `color-mix(in srgb, ${color} 12%, transparent)`);
        root.style.setProperty('--primary-border', `color-mix(in srgb, ${color} 25%, transparent)`);

        // Ring + input focus
        root.style.setProperty('--ring', color);

        // Sidebar tokens
        root.style.setProperty('--sidebar-primary', color);
        root.style.setProperty('--sidebar-accent-foreground', color);
        root.style.setProperty('--sidebar-ring', color);

        // Light mode accent (orange-50 tint of selected color)
        const isDark = root.classList.contains('dark');
        if (isDark) {
            root.style.setProperty('--accent', `color-mix(in srgb, ${color} 15%, transparent)`);
            root.style.setProperty('--accent-foreground', color);
            root.style.setProperty('--sidebar-accent', `color-mix(in srgb, ${color} 15%, transparent)`);
        } else {
            root.style.setProperty('--accent', `color-mix(in srgb, ${color} 8%, white)`);
            root.style.setProperty('--accent-foreground', color);
            root.style.setProperty('--sidebar-accent', `color-mix(in srgb, ${color} 8%, white)`);
        }
    }

    // Apply immediately on composable call (handles first load)
    applyTheme(primaryColor.value);

    // Re-apply whenever the saved theme changes (handles save without refresh)
    watch(primaryColor, (color) => {
        applyTheme(color);
    });
}
