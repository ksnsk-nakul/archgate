<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Clock } from 'lucide-vue-next';
import { show } from '@/routes/app/tasks';
import type { Task } from '@/stores/useTaskStore';

defineProps<{
    task: { data: Task };
    logs: {
        data: Array<{
            id: number;
            action: string;
            changes: Record<string, unknown> | null;
            created_at: string;
            user?: { data?: { name: string } };
        }>;
    };
}>();
</script>

<template>
    <Head title="Task history" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-app">
            <Link
                :href="show(task.data)"
                class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-app border border-app hover:border-slate-600 px-3 py-2 rounded-lg transition-colors"
            >
                <ArrowLeft class="size-3.5" /> Back to task
            </Link>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">Tasks</p>
                <h1 class="text-xl font-bold text-app truncate max-w-sm" style="font-family: Manrope, sans-serif;">History: {{ task.data.title }}</h1>
            </div>
        </div>

        <div class="px-6 py-6 max-w-3xl">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app flex items-center gap-2">
                    <Clock class="size-4 text-purple-400" />
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Activity log</h2>
                    <span class="ml-auto text-xs text-slate-500">{{ logs.data.length }} event{{ logs.data.length !== 1 ? 's' : '' }}</span>
                </div>

                <div class="divide-y divide-app">
                    <div v-for="log in logs.data" :key="log.id" class="px-6 py-4">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div>
                                <p class="text-sm font-semibold text-app">{{ log.action }}</p>
                                <p v-if="log.user?.data?.name" class="text-xs text-slate-500 mt-0.5">by {{ log.user.data.name }}</p>
                            </div>
                            <p class="text-xs text-slate-600 shrink-0">{{ log.created_at }}</p>
                        </div>
                        <pre v-if="log.changes" class="text-xs text-slate-500 bg-app-elevated rounded-lg px-3 py-2 overflow-x-auto whitespace-pre-wrap">{{ JSON.stringify(log.changes, null, 2) }}</pre>
                    </div>

                    <div v-if="logs.data.length === 0" class="px-6 py-12 text-center">
                        <Clock class="mx-auto mb-2 size-7 text-slate-600" />
                        <p class="text-sm text-slate-500">No activity logged yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
