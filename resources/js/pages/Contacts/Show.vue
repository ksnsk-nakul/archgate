<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Building2, Mail, Pencil, Phone, Trash2, TrendingUp } from 'lucide-vue-next';

type Contact = { id: number; name: string; email?: string | null; phone?: string | null; organization_id?: number | null };
type Deal = { id: number; title: string; value?: number | null; stage?: { id: number; name: string } | null };

const props = defineProps<{
    contact: { data: Contact };
    deals: { data: Deal[] };
}>();

function deleteContact(): void {
    if (!confirm('Delete this contact? This cannot be undone.')) { return; }
    router.delete(`/contacts/${props.contact.data.id}`, { onSuccess: () => router.visit('/contacts') });
}
</script>

<template>
    <Head :title="contact.data.name" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 md:px-6 border-b border-app">
            <div class="flex items-center gap-3">
                <Link href="/contacts" class="flex items-center gap-1.5 text-xs font-semibold text-app-muted hover:text-app border border-app hover:border-app px-3 py-2 rounded-lg transition-colors">
                    <ArrowLeft class="size-3.5" /> Contacts
                </Link>
                <div>
                    <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">CRM</p>
                    <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">{{ contact.data.name }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <Link :href="`/contacts/${contact.data.id}/edit`" class="flex items-center gap-1.5 text-xs font-semibold text-app-muted border border-app hover:text-app hover:border-app px-3 py-2 rounded-lg transition-colors">
                    <Pencil class="size-3.5" /> Edit
                </Link>
                <button class="flex items-center gap-1.5 text-xs font-semibold text-red-400 border border-red-500/20 hover:bg-red-500/10 px-3 py-2 rounded-lg transition-colors" @click="deleteContact">
                    <Trash2 class="size-3.5" /> Delete
                </button>
            </div>
        </div>

        <div class="px-4 py-6 md:px-6 grid gap-4 lg:grid-cols-3">
            <!-- Contact info -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-5 py-4 border-b border-app">
                    <div class="size-10 rounded-full bg-[var(--primary)]/10 flex items-center justify-center mb-3">
                        <span class="text-base font-bold text-[var(--primary)]">{{ contact.data.name?.[0]?.toUpperCase() ?? '?' }}</span>
                    </div>
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">{{ contact.data.name }}</h2>
                </div>
                <div class="px-5 py-4 flex flex-col gap-3">
                    <div class="flex items-center gap-2.5 text-sm">
                        <Mail class="size-3.5 text-app-muted shrink-0" />
                        <a v-if="contact.data.email" :href="`mailto:${contact.data.email}`" class="text-[var(--primary)] hover:underline">{{ contact.data.email }}</a>
                        <span v-else class="text-app-muted">No email</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-sm">
                        <Phone class="size-3.5 text-app-muted shrink-0" />
                        <span v-if="contact.data.phone" class="text-app">{{ contact.data.phone }}</span>
                        <span v-else class="text-app-muted">No phone</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-sm">
                        <Building2 class="size-3.5 text-app-muted shrink-0" />
                        <span v-if="contact.data.organization_id" class="text-app">Org #{{ contact.data.organization_id }}</span>
                        <span v-else class="text-app-muted">No organization</span>
                    </div>
                </div>
            </div>

            <!-- Linked deals -->
            <div class="lg:col-span-2 rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-5 py-4 border-b border-app flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <TrendingUp class="size-4 text-amber-400" />
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Linked deals</h2>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated">{{ deals.data.length }}</span>
                </div>
                <div class="divide-y divide-app">
                    <div v-for="deal in deals.data" :key="deal.id" class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-app truncate">{{ deal.title }}</p>
                            <p class="text-xs text-app-muted mt-0.5">{{ deal.stage?.name ?? 'No stage' }}</p>
                        </div>
                        <p v-if="deal.value != null" class="text-sm font-bold text-app shrink-0">${{ Number(deal.value).toLocaleString() }}</p>
                        <p v-else class="text-xs text-app-muted shrink-0">No value</p>
                    </div>
                    <div v-if="deals.data.length === 0" class="px-5 py-10 text-center">
                        <TrendingUp class="mx-auto mb-2 size-6 text-app-muted" />
                        <p class="text-sm text-app-muted">No deals linked yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
