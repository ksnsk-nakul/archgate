<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, TrendingUp } from 'lucide-vue-next';
import { computed } from 'vue';
import { useCrmStore } from '@/stores/useCrmStore';
import type { Deal } from '@/stores/useCrmStore';

const crmStore = useCrmStore();

const props = defineProps<{
    deals: { data: Deal[] };
    stages?: { data: Array<{ id: number; name: string; order: number }> };
    contacts?: { data: Array<{ id: number; name: string }> };
}>();

crmStore.hydrateDeals(props.deals.data);

const groupedDeals = computed(() =>
    crmStore.deals.reduce<Record<string, typeof crmStore.deals>>((groups, deal) => {
        const stage = deal.stage?.name ?? deal.status ?? 'Unassigned';
        groups[stage] = groups[stage] ?? [];
        groups[stage].push(deal);
        return groups;
    }, {}),
);

function stageTotal(deals: Deal[]): string {
    const total = deals.reduce((sum, d) => sum + (Number(d.value) || 0), 0);
    return total.toLocaleString();
}

function deleteDeal(id: number): void {
    if (!confirm('Delete this deal? This cannot be undone.')) {
        return;
    }

    router.delete(`/deals/${id}`, {
        onSuccess: () => {
            crmStore.hydrateDeals(crmStore.deals.filter((d) => d.id !== id));
        },
    });
}

const stageColors = [
    'border-blue-500/20 bg-blue-500/5',
    'border-amber-500/20 bg-amber-500/5',
    'border-emerald-500/20 bg-emerald-500/5',
    'border-purple-500/20 bg-purple-500/5',
    'border-red-500/20 bg-red-500/5',
];
</script>

<template>
    <Head title="Deals" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-4 py-4 md:px-6 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">CRM</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Deals</h1>
            </div>
            <Link
                href="/deals/create"
                class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
            >
                <Plus class="size-3.5" />
                New deal
            </Link>
        </div>

        <div class="px-4 py-6 md:px-6">
            <!-- Pipeline board -->
            <div v-if="Object.keys(groupedDeals).length > 0" class="grid gap-4 lg:grid-cols-3">
                <div
                    v-for="(stageDeals, stage, idx) in groupedDeals"
                    :key="stage"
                    class="rounded-xl border border-app bg-app-surface overflow-hidden"
                >
                    <!-- Stage header -->
                    <div class="px-5 py-4 border-b border-app flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">{{ stage }}</p>
                            <p class="text-xs text-app-muted mt-0.5">${{ stageTotal(stageDeals) }} total</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated">
                            {{ stageDeals.length }}
                        </span>
                    </div>

                    <!-- Deal cards -->
                    <div class="p-3 flex flex-col gap-2">
                        <div
                            v-for="deal in stageDeals"
                            :key="deal.id"
                            class="rounded-lg border p-3 flex items-center justify-between gap-2"
                            :class="stageColors[idx % stageColors.length]"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-app truncate">{{ deal.title }}</p>
                                <p v-if="deal.value" class="text-xs text-app-muted mt-0.5">
                                    ${{ Number(deal.value).toLocaleString() }}
                                </p>
                                <p v-else class="text-xs text-app-muted mt-0.5">No value</p>
                            </div>
                            <button
                                class="text-xs font-semibold text-red-400 hover:text-red-300 transition-colors shrink-0"
                                @click="deleteDeal(deal.id)"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="size-14 rounded-2xl bg-amber-500/10 flex items-center justify-center mb-4">
                    <TrendingUp class="size-7 text-amber-400" />
                </div>
                <h3 class="text-base font-bold text-app mb-1" style="font-family: Manrope, sans-serif;">No deals yet</h3>
                <p class="text-sm text-app-muted mb-4">Add your first deal to start tracking your pipeline.</p>
                <Link
                    href="/deals/create"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus class="size-3.5" /> Create first deal
                </Link>
            </div>
        </div>
    </div>
</template>
