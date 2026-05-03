<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookMarked, Lock, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useLearningStore } from '@/stores/useLearningStore';
import type { LibraryContent, SubscriptionPlan } from '@/stores/useLearningStore';

const learningStore = useLearningStore();
const accessFilter = ref('all');

const props = defineProps<{
    content: { data: LibraryContent[] };
    subscriptions: { data: SubscriptionPlan[] };
    canCreate?: boolean;
}>();

learningStore.hydrateLibrary(props.content.data, props.subscriptions.data);

const filteredContent = computed(() =>
    learningStore.content.filter(
        (item) => accessFilter.value === 'all' || item.access_level === accessFilter.value,
    ),
);

const typeIcon: Record<string, string> = {
    article: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    video: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
    pdf: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
    audio: 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3',
};

const accessColor: Record<string, string> = {
    free: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
    basic: 'text-blue-400 bg-blue-500/10 border-blue-500/20',
    premium: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
};
</script>

<template>
    <Head title="Library" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 md:px-6 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Knowledge</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Library</h1>
            </div>
            <div class="flex items-center gap-3">
                <!-- Access filter -->
                <select
                    v-model="accessFilter"
                    class="input-app text-xs rounded-lg px-3 py-2 border transition-colors"
                >
                    <option value="all">All access levels</option>
                    <option value="free">Free</option>
                    <option value="basic">Basic</option>
                    <option value="premium">Premium</option>
                </select>
                <Link
                    v-if="props.canCreate"
                    href="/library/create"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
                >
                    <Plus class="size-3.5" />
                    Add item
                </Link>
            </div>
        </div>

        <div class="px-4 py-6 md:px-6 flex flex-col gap-8">
            <!-- Content grid -->
            <div v-if="filteredContent.length > 0" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="item in filteredContent"
                    :key="item.id"
                    class="rounded-xl border border-app bg-app-surface overflow-hidden hover:border-[var(--primary)]/30 transition-colors"
                >
                    <div class="px-5 py-4 border-b border-app flex items-start justify-between gap-3">
                        <div class="size-9 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                            <svg class="size-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="typeIcon[item.type ?? 'article'] ?? typeIcon.article" />
                            </svg>
                        </div>
                        <Lock
                            v-if="item.access_level && item.access_level !== 'free'"
                            class="size-3.5 text-app-muted mt-1 shrink-0"
                        />
                    </div>

                    <div class="px-5 py-4 flex flex-col gap-3">
                        <p class="text-sm font-bold text-app leading-snug" style="font-family: Manrope, sans-serif;">{{ item.title }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated capitalize">
                                {{ item.type ?? 'Content' }}
                            </span>
                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full border capitalize"
                                :class="accessColor[item.access_level ?? 'free'] ?? accessColor.free"
                            >
                                {{ item.access_level ?? 'free' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="size-14 rounded-2xl bg-blue-500/10 flex items-center justify-center mb-4">
                    <BookMarked class="size-7 text-blue-400" />
                </div>
                <h3 class="text-base font-bold text-app mb-1" style="font-family: Manrope, sans-serif;">No content found</h3>
                <p class="text-sm text-app-muted mb-4">
                    {{ accessFilter !== 'all' ? 'No content matches this access level.' : 'The library is empty.' }}
                </p>
                <Link
                    v-if="props.canCreate"
                    href="/library/create"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus class="size-3.5" /> Add first item
                </Link>
            </div>

            <!-- Subscription plans -->
            <div v-if="learningStore.subscriptions.length > 0">
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-3">Your subscriptions</p>
                <div class="grid gap-4 md:grid-cols-3">
                    <div
                        v-for="plan in learningStore.subscriptions"
                        :key="plan.id"
                        class="rounded-xl border border-app bg-app-surface px-5 py-4 flex items-center justify-between gap-4"
                    >
                        <div>
                            <p class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">{{ plan.name }}</p>
                            <p class="text-xs text-app-muted mt-0.5">
                                {{ plan.price != null ? `$${Number(plan.price).toLocaleString()}` : 'Free' }}
                            </p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full text-emerald-400 bg-emerald-500/10 border border-emerald-500/20">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
