<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { index } from '@/routes/admin/roles';
import { update } from '@/routes/admin/roles/permissions';

type Permission = {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    group?: string | null;
};

type Role = {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    permissions?: Permission[];
};

const props = defineProps<{
    role: {
        data: Role;
    };
    permissionGroups: Record<string, { data: Permission[] } | Permission[]>;
}>();

// Snapshot of permissions as they existed on page load — used for diff display
const originalIds = new Set<number>(props.role.data.permissions?.map((p) => p.id) ?? []);

// Mutable reactive set so toggling updates the visual immediately
const checkedIds = ref<Set<number>>(new Set(originalIds));

const selectedPermissionIds = computed(() => [...checkedIds.value]);

/** True when current selection differs from what was saved in the DB */
const hasChanges = computed(() => {
    if (checkedIds.value.size !== originalIds.size) { return true; }
    for (const id of checkedIds.value) {
        if (!originalIds.has(id)) { return true; }
    }
    return false;
});

function togglePermission(id: number) {
    if (checkedIds.value.has(id)) {
        checkedIds.value.delete(id);
    } else {
        checkedIds.value.add(id);
    }
    // Trigger Vue reactivity — Sets aren't deeply reactive by default
    checkedIds.value = new Set(checkedIds.value);
}

/**
 * Visual state for a single permission checkbox:
 * - 'checked'   → currently enabled (orange tick)
 * - 'was-on'    → was enabled on load, now deselected (greyed tick)
 * - 'unchecked' → never assigned in this session (empty box)
 */
function checkState(id: number): 'checked' | 'was-on' | 'unchecked' {
    if (checkedIds.value.has(id)) { return 'checked'; }
    if (originalIds.has(id)) { return 'was-on'; }
    return 'unchecked';
}

const groups = computed(() =>
    Object.entries(props.permissionGroups).map(([group, permissions]) => {
        let perms: Permission[] = [];
        if (Array.isArray(permissions)) {
            perms = permissions;
        } else if (permissions && Array.isArray((permissions as { data: Permission[] }).data)) {
            perms = (permissions as { data: Permission[] }).data;
        }
        return { group, permissions: perms };
    }),
);

const groupIcon: Record<string, string> = {
    Settings: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
    Roles: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    Projects: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
    Tasks: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    CRM: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    Learning: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
};

const defaultIcon = 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z';
</script>

<template>
    <Head :title="`${role.data.name} — Policy`" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Admin › Roles</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">
                    {{ role.data.name }} — Policy
                </h1>
            </div>
            <Link
                :href="index()"
                class="flex items-center gap-2 text-sm text-app-muted hover:text-white border border-app hover:border-slate-500 px-4 py-2 rounded-lg transition-colors"
            >
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to roles
            </Link>
        </div>

        <Form v-bind="update.form(role.data)" class="px-6 py-6 flex flex-col gap-5" v-slot="{ processing }">

            <!-- Role summary banner -->
            <div class="rounded-xl border border-slate-800 bg-[#0d1c2d] px-6 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-[var(--primary)]/10 border border-[var(--primary)]/20 flex items-center justify-center shrink-0">
                    <svg class="size-5 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-app">{{ role.data.name }}</p>
                    <p class="text-xs text-app-muted">{{ role.data.description || role.data.slug }} · {{ selectedPermissionIds.length }} permissions selected</p>
                </div>
            </div>

            <!-- Permission groups -->
            <div
                v-for="group in groups"
                :key="group.group"
                class="rounded-xl border border-app bg-app-surface overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-app-elevated flex items-center justify-center shrink-0">
                        <svg class="size-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="groupIcon[group.group] ?? defaultIcon" />
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">{{ group.group }}</h2>
                    <span class="ml-auto text-xs px-2 py-0.5 rounded-full transition-colors"
                          :class="group.permissions.some(p => checkedIds.has(p.id))
                              ? 'text-[var(--primary)]/80 bg-[var(--primary)]/10'
                              : 'text-slate-600 bg-slate-800'">
                        {{ group.permissions.filter(p => checkedIds.has(p.id)).length }} / {{ group.permissions.length }}
                    </span>
                </div>

                <div class="px-6 py-4 grid gap-3 md:grid-cols-2">
                    <label
                        v-for="permission in group.permissions"
                        :key="permission.id"
                        class="flex items-start gap-3 rounded-lg border p-3.5 cursor-pointer transition-all select-none"
                        :class="checkedIds.has(permission.id)
                            ? 'border-[var(--primary)]/30 bg-[var(--primary)]/5'
                            : 'border-slate-800 hover:border-slate-700 hover:bg-slate-800/30'"
                        @click.prevent="togglePermission(permission.id)"
                    >
                        <!-- Hidden real checkbox for form submission -->
                        <input
                            type="checkbox"
                            name="permissions[]"
                            :value="permission.id"
                            :checked="checkedIds.has(permission.id)"
                            class="sr-only"
                            tabindex="-1"
                        />
                        <!-- Custom visual checkbox — 3 states -->
                        <div class="mt-0.5 shrink-0">
                            <!-- CHECKED: solid orange with tick -->
                            <div
                                class="size-4 rounded border-2 flex items-center justify-center transition-all duration-150"
                                :class="checkedIds.has(permission.id)
                                    ? 'bg-[var(--primary)] border-[var(--primary)] shadow-[0_0_8px_rgba(247,96,13,0.4)]'
                                    : 'border-slate-600 bg-slate-900 hover:border-slate-500'"
                            >
                                <svg class="size-2.5 text-app" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <!-- WAS-ON: greyed-out tick (was previously enabled, now deselected) -->
                            <div
                                v-else-if="checkState(permission.id) === 'was-on'"
                                class="size-4 rounded border-2 border-app bg-app-elevated flex items-center justify-center transition-all duration-150"
                                title="Previously enabled — will be removed on save"
                            >
                                <svg class="size-2.5 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <!-- UNCHECKED: empty box -->
                            <div
                                v-else
                                class="size-4 rounded border-2 border-slate-600 bg-app-elevated hover:border-slate-500 flex items-center justify-center transition-all duration-150"
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium leading-tight"
                               :class="{
                                   'text-white': checkState(permission.id) === 'checked',
                                   'text-app-muted line-through decoration-slate-600': checkState(permission.id) === 'was-on',
                                   'text-app-muted': checkState(permission.id) === 'unchecked',
                               }">
                                {{ permission.name }}
                            </p>
                            <p class="text-xs font-mono mt-0.5"
                               :class="checkedIds.has(permission.id) ? 'text-[var(--primary)]/70' : 'text-slate-600'">
                                {{ permission.slug }}
                            </p>
                            <!-- "Was enabled" hint -->
                            <p v-if="checkState(permission.id) === 'was-on'" class="text-xs text-amber-500/70 mt-0.5 font-medium">
                                Previously enabled · will be removed
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Save -->
            <div class="flex items-center gap-4 pb-4">
                <button
                    type="submit"
                    :disabled="processing"
                    class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                >
                    <svg v-if="processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <svg v-else class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ hasChanges ? 'Update permissions' : 'No changes' }}
                </button>
                <Link :href="index()" class="text-sm text-app-muted hover:text-app-muted transition-colors">
                    Cancel
                </Link>
                <!-- Change summary pill -->
                <span v-if="hasChanges" class="text-xs text-amber-400/80 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full font-medium">
                    {{ selectedPermissionIds.length - originalIds.size > 0 ? `+${selectedPermissionIds.length - originalIds.size}` : selectedPermissionIds.length - originalIds.size }} changes pending
                </span>
            </div>
        </Form>
    </div>
</template>
