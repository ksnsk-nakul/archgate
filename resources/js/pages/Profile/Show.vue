<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Pencil, User } from 'lucide-vue-next';
import { useUserStore } from '@/stores/useUserStore';

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const userStore = useUserStore();

const props = defineProps<{
    profile: {
        data: {
            id: number;
            name: string;
            email: string;
        };
    };
}>();

userStore.hydrateUser(props.profile.data);
</script>

<template>
    <Head title="Profile" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Account</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Profile</h1>
            </div>
            <Link
                href="/profile/edit"
                class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
            >
                <Pencil class="size-3.5" /> Edit profile
            </Link>
        </div>

        <div class="px-6 py-6 max-w-2xl">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-5 py-4 border-b border-app flex items-center gap-3">
                    <div class="size-10 rounded-full bg-[var(--primary)]/10 flex items-center justify-center shrink-0">
                        <span class="text-base font-bold text-[var(--primary)]">
                            {{ (userStore.user?.name ?? currentUser.name)?.[0]?.toUpperCase() ?? '?' }}
                        </span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">
                            {{ userStore.user?.name ?? currentUser.name }}
                        </h2>
                        <p class="text-xs text-app-muted">{{ userStore.user?.email ?? currentUser.email }}</p>
                    </div>
                </div>
                <div class="px-5 py-4 flex flex-col gap-3">
                    <div class="flex items-center justify-between gap-4 text-sm py-1.5 border-b border-app">
                        <span class="text-app-muted font-medium">Email</span>
                        <span class="text-app font-semibold">{{ userStore.user?.email ?? currentUser.email }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 text-sm py-1.5">
                        <span class="text-app-muted font-medium">Role</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated capitalize">
                            {{ userStore.role ?? 'user' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
