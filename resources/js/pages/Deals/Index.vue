<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { TrendingUp } from 'lucide-vue-next';
import { computed } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useCrmStore } from '@/stores/useCrmStore';
import type { Deal } from '@/stores/useCrmStore';

const crmStore = useCrmStore();

const props = defineProps<{
    deals: {
        data: Deal[];
    };
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
</script>

<template>
    <Head title="Deals" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader title="Deals" description="View pipeline stages and active deal value." />
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <Card v-for="(stageDeals, stage) in groupedDeals" :key="stage" class="rounded-lg">
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-base">
                        <span>{{ stage }}</span>
                        <Badge variant="secondary">{{ stageDeals.length }}</Badge>
                    </CardTitle>
                    <p class="text-xs text-muted-foreground">${{ stageTotal(stageDeals) }} total</p>
                </CardHeader>
                <CardContent class="flex flex-col gap-2">
                    <div v-for="deal in stageDeals" :key="deal.id" class="flex items-center justify-between rounded-md border p-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium">{{ deal.title }}</p>
                            <p v-if="deal.value" class="text-sm text-muted-foreground">
                                ${{ Number(deal.value).toLocaleString() }}
                            </p>
                            <p v-else class="text-sm text-muted-foreground">No value</p>
                        </div>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="ml-2 shrink-0 text-xs text-destructive hover:text-destructive"
                            @click="deleteDeal(deal.id)"
                        >
                            Delete
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="!crmStore.loading && crmStore.deals.length === 0" class="rounded-lg border border-dashed p-12 text-center">
            <TrendingUp class="mx-auto mb-3 size-8 text-muted-foreground" />
            <p class="mb-1 font-medium">No deals yet</p>
            <p class="mb-4 text-sm text-muted-foreground">Add your first deal to start tracking your pipeline.</p>
        </div>
    </div>
</template>
