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

// Mutable reactive set so toggling updates the visual immediately
const checkedIds = ref<Set<number>>(
    new Set(props.role.data.permissions?.map((p) => p.id) ?? []),
);

const selectedPermissionIds = computed(() => [...checkedIds.value]);

function togglePermission(id: number) {
    if (checkedIds.value.has(id)) {
        checkedIds.value.delete(id);
    } else {
        checkedIds.value.add(id);
    }
    // Trigger Vue reactivity — Sets aren't deeply reactive by default
    checkedIds.value = new Set(checkedIds.value);
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

    <div class="flex flex-col min-h-full bg-[#051424] text-[#d4e4fa]" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-800">
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">Admin › Roles</p>
                <h1 class="text-xl font-bold text-white" style="font-family: Manrope, sans-serif;">
                    {{ role.data.name }} — Policy
                </h1>
            </div>
            <Link
                :href="index()"
                class="flex items-center gap-2 text-sm text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 px-4 py-2 rounded-lg transition-colors"
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
                <div class="w-10 h-10 rounded-xl bg-[#f7600d]/10 border border-[#f7600d]/20 flex items-center justify-center shrink-0">
                    <svg class="size-5 text-[#f7600d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-white">{{ role.data.name }}</p>
                    <p class="text-xs text-slate-500">{{ role.data.description || role.data.slug }} · {{ selectedPermissionIds.length }} permissions selected</p>
                </div>
            </div>

            <!-- Permission groups -->
            <div
                v-for="group in groups"
                :key="group.group"
                class="rounded-xl border border-slate-800 bg-[#0d1c2d] overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-slate-800 flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-800 flex items-center justify-center shrink-0">
                        <svg class="size-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="groupIcon[group.group] ?? defaultIcon" />
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-white" style="font-family: Manrope, sans-serif;">{{ group.group }}</h2>
                    <span class="ml-auto text-xs px-2 py-0.5 rounded-full transition-colors"
                          :class="group.permissions.some(p => checkedIds.has(p.id))
                              ? 'text-[#f7600d]/80 bg-[#f7600d]/10'
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
                            ? 'border-[#f7600d]/30 bg-[#f7600d]/5'
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
                        <!-- Custom visual checkbox -->
                        <div class="mt-0.5 shrink-0">
                            <div
                                class="size-4 rounded border-2 flex items-center justify-center transition-all duration-150"
                                :class="checkedIds.has(permission.id)
                                    ? 'bg-[#f7600d] border-[#f7600d] shadow-[0_0_8px_rgba(247,96,13,0.4)]'
                                    : 'border-slate-600 bg-slate-900 hover:border-slate-500'"
                            >
                                <svg v-if="checkedIds.has(permission.id)" class="size-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium leading-tight"
                               :class="checkedIds.has(permission.id) ? 'text-white' : 'text-slate-300'">
                                {{ permission.name }}
                            </p>
                            <p class="text-xs font-mono mt-0.5"
                               :class="checkedIds.has(permission.id) ? 'text-[#f7600d]/70' : 'text-slate-600'">
                                {{ permission.slug }}
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
                    class="flex items-center gap-2 bg-[#f7600d] hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                >
                    <svg v-if="processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <svg v-else class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update role policy
                </button>
                <Link :href="index()" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">
                    Cancel
                </Link>
            </div>
        </Form>
    </div>
</template>
