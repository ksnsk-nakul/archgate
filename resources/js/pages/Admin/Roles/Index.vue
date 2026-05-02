<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { edit } from '@/routes/admin/roles/permissions';
import { store } from '@/routes/admin/roles/subadmin';

type Permission = {
    id: number;
    name: string;
    slug: string;
    group?: string | null;
};

type Role = {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    is_system?: boolean;
    users_count?: number;
    permissions?: Permission[];
};

defineProps<{
    roles: {
        data: Role[];
    };
}>();

const systemRoleBadge: Record<string, string> = {
    superadmin: 'bg-[var(--primary)]/15 text-[var(--primary)] border-[var(--primary)]/25',
    admin: 'bg-amber-500/15 text-amber-400 border-amber-500/25',
    author: 'bg-blue-500/15 text-blue-400 border-blue-500/25',
    tutor: 'bg-violet-500/15 text-violet-400 border-violet-500/25',
    user: 'bg-slate-500/15 text-app-muted border-slate-500/25',
};
</script>

<template>
    <Head title="Roles" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Admin</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Roles & Permissions</h1>
            </div>
        </div>

        <div class="px-6 py-6 flex flex-col gap-6">

            <!-- Create subadmin card -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Create subadmin role</h2>
                    <p class="text-xs text-app-muted mt-0.5">Custom roles can be assigned tailored permission policies.</p>
                </div>
                <div class="px-6 py-5">
                    <Form
                        v-bind="store.form()"
                        class="grid gap-4 md:grid-cols-[1fr_1.5fr_auto] items-end"
                        v-slot="{ errors, processing }"
                    >
                        <div class="flex flex-col gap-1.5">
                            <label for="name" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Name</label>
                            <input
                                id="name"
                                name="name"
                                placeholder="Operations"
                                required
                                class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors"
                            />
                            <InputError :message="errors.name" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="description" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Description</label>
                            <input
                                id="description"
                                name="description"
                                placeholder="Manages operational tasks"
                                class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]/20 transition-colors"
                            />
                            <InputError :message="errors.description" />
                        </div>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors whitespace-nowrap"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create role
                        </button>
                    </Form>
                </div>
            </div>

            <!-- Role list -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Role list</h2>
                    <p class="text-xs text-app-muted mt-0.5">Open the policy page to update a role's permissions. Superadmin is locked.</p>
                </div>

                <!-- Column headers -->
                <div class="hidden md:grid grid-cols-[1fr_100px_60px_80px_120px] px-6 py-2.5 bg-app-elevated/50 border-b border-app text-xs text-app-muted font-semibold uppercase tracking-wider gap-4">
                    <span>Role</span>
                    <span>Type</span>
                    <span class="text-center">Users</span>
                    <span class="text-center">Perms</span>
                    <span class="text-right">Policy</span>
                </div>

                <div class="divide-y divide-app/60">
                    <div
                        v-for="role in roles.data"
                        :key="role.id"
                        class="grid grid-cols-1 md:grid-cols-[1fr_100px_60px_80px_120px] items-center px-6 py-4 hover:bg-app-elevated/20 transition-colors gap-4 group"
                    >
                        <!-- Name + description -->
                        <div>
                            <p class="text-sm font-semibold text-app">{{ role.name }}</p>
                            <p class="text-xs text-app-muted">{{ role.description || role.slug }}</p>
                        </div>

                        <!-- Type badge -->
                        <div>
                            <span
                                :class="role.is_system ? (systemRoleBadge[role.slug] ?? 'bg-slate-500/15 text-app-muted border-slate-500/25') : 'bg-app-elevated text-app-muted border-app'"
                                class="inline-flex items-center text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full border"
                            >
                                {{ role.is_system ? role.slug === 'superadmin' ? 'Super' : 'System' : 'Custom' }}
                            </span>
                        </div>

                        <!-- Users count -->
                        <div class="text-center">
                            <span class="text-sm text-app-muted font-medium">{{ role.users_count ?? 0 }}</span>
                        </div>

                        <!-- Permissions count -->
                        <div class="text-center">
                            <span class="text-sm text-app-muted font-medium">{{ role.permissions?.length ?? 0 }}</span>
                        </div>

                        <!-- Policy action -->
                        <div class="flex justify-end">
                            <Link
                                v-if="role.slug !== 'superadmin'"
                                :href="edit(role)"
                                class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-[var(--primary)] border border-slate-700 hover:border-[var(--primary)]/40 px-3 py-1.5 rounded-lg transition-colors"
                            >
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Manage policy
                            </Link>
                            <span v-else class="flex items-center gap-1 text-xs text-slate-700">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Locked
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
