<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ClipboardList, Pencil } from 'lucide-vue-next';
import { edit } from '@/routes/app/projects';
import type { Project } from '@/stores/useProjectStore';

defineProps<{
    project: { data: Project };
}>();

const statusColor: Record<string, string> = {
    active: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
    completed: 'text-blue-400 bg-blue-500/10 border-blue-500/20',
    archived: 'text-app-muted bg-app-elevated border-app',
};
</script>

<template>
    <Head title="Project details" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 md:px-6 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Projects</p>
                <h1 class="text-xl font-bold text-app truncate max-w-md" style="font-family: Manrope, sans-serif;">{{ project.data.name }}</h1>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <Link
                    :href="edit(project.data)"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
                >
                    <Pencil class="size-3.5" /> Edit
                </Link>
                <Link
                    href="/tasks"
                    class="flex items-center gap-1.5 text-xs font-semibold text-app-muted border border-app hover:text-app hover:border-app px-3 py-2 rounded-lg transition-colors"
                >
                    <ClipboardList class="size-3.5" /> View tasks
                </Link>
            </div>
        </div>

        <div class="px-4 py-6 md:px-6 max-w-3xl">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                    <ClipboardList class="size-4 text-amber-400" />
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Project details</h2>
                </div>
                <div class="px-6 py-5 flex flex-col gap-4">
                    <div>
                        <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Status</p>
                        <span
                            class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                            :class="statusColor[project.data.status ?? 'active'] ?? 'text-app-muted bg-app-elevated border-app'"
                        >
                            {{ project.data.status ?? 'active' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Name</p>
                        <p class="text-sm font-medium text-app">{{ project.data.name }}</p>
                    </div>
                    <div v-if="project.data.description">
                        <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Description</p>
                        <p class="text-sm text-app-muted leading-relaxed">{{ project.data.description }}</p>
                    </div>
                    <div v-else>
                        <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Description</p>
                        <p class="text-sm text-app-muted italic">No description yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
