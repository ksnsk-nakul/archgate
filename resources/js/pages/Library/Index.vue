<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { BookMarked, Lock } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useLearningStore } from '@/stores/useLearningStore';
import type { LibraryContent, SubscriptionPlan } from '@/stores/useLearningStore';

const learningStore = useLearningStore();
const accessFilter = ref('all');

const props = defineProps<{
    content: {
        data: LibraryContent[];
    };
    subscriptions: {
        data: SubscriptionPlan[];
    };
}>();

learningStore.hydrateLibrary(props.content.data, props.subscriptions.data);

const filteredContent = computed(() =>
    learningStore.content.filter(
        (item) => accessFilter.value === 'all' || item.access_level === accessFilter.value,
    ),
);

const accessBadgeVariant = (level: string | null | undefined): 'default' | 'secondary' | 'outline' => {
    if (level === 'free') {
        return 'secondary';
    }

    if (level === 'premium') {
        return 'default';
    }

    return 'outline';
};
</script>

<template>
    <Head title="Library" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <PageHeader title="Library" description="Browse free and premium knowledge resources." />
            <select v-model="accessFilter" class="h-10 rounded-md border bg-background px-3 text-sm">
                <option value="all">All access levels</option>
                <option value="free">Free</option>
                <option value="basic">Basic</option>
                <option value="premium">Premium</option>
            </select>
        </div>

        <!-- Content grid -->
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Card v-for="item in filteredContent" :key="item.id" class="rounded-lg">
                <CardHeader>
                    <div class="flex items-start justify-between gap-2">
                        <CardTitle class="text-base leading-snug">{{ item.title }}</CardTitle>
                        <Lock v-if="item.access_level && item.access_level !== 'free'" class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                    </div>
                </CardHeader>
                <CardContent class="flex flex-wrap gap-2">
                    <Badge variant="outline">{{ item.type ?? 'Content' }}</Badge>
                    <Badge :variant="accessBadgeVariant(item.access_level)">{{ item.access_level ?? 'free' }}</Badge>
                </CardContent>
            </Card>
        </div>

        <div v-if="filteredContent.length === 0" class="rounded-lg border border-dashed p-12 text-center">
            <BookMarked class="mx-auto mb-3 size-8 text-muted-foreground" />
            <p class="mb-1 font-medium">No content found</p>
            <p class="text-sm text-muted-foreground">
                {{ accessFilter !== 'all' ? 'No content matches this access level.' : 'The library is empty.' }}
            </p>
        </div>

        <!-- Subscription plans -->
        <div v-if="learningStore.subscriptions.length > 0">
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Your subscriptions</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <Card v-for="plan in learningStore.subscriptions" :key="plan.id" class="rounded-lg">
                    <CardHeader>
                        <CardTitle class="text-base">{{ plan.name }}</CardTitle>
                    </CardHeader>
                    <CardContent class="flex items-center justify-between gap-4">
                        <span class="text-sm text-muted-foreground">
                            {{ plan.price != null ? `$${Number(plan.price).toLocaleString()}` : 'Free' }}
                        </span>
                        <Badge variant="secondary">Active</Badge>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
