<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Mail, Phone, Plus, UserRound } from 'lucide-vue-next';
import { useCrmStore } from '@/stores/useCrmStore';
import type { Contact } from '@/stores/useCrmStore';

const crmStore = useCrmStore();

const props = defineProps<{
    contacts: { data: Contact[] };
}>();

crmStore.hydrateContacts(props.contacts.data);

function deleteContact(id: number): void {
    if (!confirm('Delete this contact? This cannot be undone.')) {
        return;
    }

    router.delete(`/contacts/${id}`, {
        onSuccess: () => {
            crmStore.hydrateContacts(crmStore.contacts.filter((c) => c.id !== id));
        },
    });
}
</script>

<template>
    <Head title="Contacts" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">CRM</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Contacts</h1>
            </div>
            <Link
                href="/contacts/create"
                class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
            >
                <Plus class="size-3.5" />
                New contact
            </Link>
        </div>

        <div class="px-6 py-6">
            <!-- Contact grid -->
            <div v-if="crmStore.contacts.length > 0" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="contact in crmStore.contacts"
                    :key="contact.id"
                    class="rounded-xl border border-app bg-app-surface overflow-hidden hover:border-[var(--primary)]/30 transition-colors"
                >
                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-app flex items-center gap-3">
                        <div class="size-9 rounded-full bg-[var(--primary)]/10 flex items-center justify-center shrink-0">
                            <span class="text-sm font-bold text-[var(--primary)]">{{ contact.name?.[0]?.toUpperCase() ?? '?' }}</span>
                        </div>
                        <Link
                            :href="`/contacts/${contact.id}`"
                            class="text-sm font-bold text-app hover:text-[var(--primary)] transition-colors leading-tight"
                            style="font-family: Manrope, sans-serif;"
                        >{{ contact.name }}</Link>
                    </div>

                    <!-- Body -->
                    <div class="px-5 py-4 flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Mail class="size-3.5 shrink-0" />
                            <span class="truncate">{{ contact.email || 'No email' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Phone class="size-3.5 shrink-0" />
                            <span>{{ contact.phone || 'No phone' }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-5 py-3 border-t border-app flex items-center gap-2">
                        <Link
                            :href="`/contacts/${contact.id}`"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold text-[var(--primary)] border border-[var(--primary)]/30 hover:bg-[var(--primary)]/5 px-3 py-1.5 rounded-lg transition-colors"
                        >
                            <UserRound class="size-3.5" /> View
                        </Link>
                        <Link
                            :href="`/contacts/${contact.id}/edit`"
                            class="flex-1 flex items-center justify-center text-xs font-semibold text-slate-400 border border-app hover:text-app hover:border-slate-600 px-3 py-1.5 rounded-lg transition-colors"
                        >
                            Edit
                        </Link>
                        <button
                            class="text-xs font-semibold text-red-400 border border-red-500/20 hover:bg-red-500/10 px-3 py-1.5 rounded-lg transition-colors"
                            @click="deleteContact(contact.id)"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="size-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-4">
                    <UserRound class="size-7 text-emerald-400" />
                </div>
                <h3 class="text-base font-bold text-app mb-1" style="font-family: Manrope, sans-serif;">No contacts yet</h3>
                <p class="text-sm text-slate-500 mb-4">Start building your CRM by adding your first contact.</p>
                <Link
                    href="/contacts/create"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus class="size-3.5" /> Add first contact
                </Link>
            </div>
        </div>
    </div>
</template>
