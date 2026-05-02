<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useProjectStore } from '@/stores/useProjectStore';
import type { Project } from '@/stores/useProjectStore';

const props = defineProps<{
    projects: {
        data: Project[];
    };
}>();

const projectStore = useProjectStore();
projectStore.hydrateProjects(props.projects.data);

type ViewMode = 'list' | 'board';
const viewMode = ref<ViewMode>('list');
const searchQuery = ref('');

const statusColor: Record<string, string> = {
    active: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25',
    completed: 'bg-blue-500/15 text-blue-400 border-blue-500/25',
    archived: 'bg-slate-500/15 text-app-muted border-slate-500/25',
};

const statusDot: Record<string, string> = {
    active: 'bg-emerald-400',
    completed: 'bg-blue-400',
    archived: 'bg-slate-500',
};

const statusLabel: Record<string, string> = {
    active: 'Active',
    completed: 'Completed',
    archived: 'Archived',
};

const filteredProjects = computed(() => {
    const q = searchQuery.value.toLowerCase();

    return projectStore.projects.filter(
        (p) => !q || p.name.toLowerCase().includes(q) || (p.description ?? '').toLowerCase().includes(q),
    );
});

const byStatus = computed(() => ({
    active: filteredProjects.value.filter((p) => p.status === 'active'),
    completed: filteredProjects.value.filter((p) => p.status === 'completed'),
    archived: filteredProjects.value.filter((p) => p.status === 'archived'),
}));

async function deleteProject(id: number): Promise<void> {
    if (!confirm('Delete this project? All associated tasks will also be removed.')) {
        return;
    }

    await projectStore.deleteProject(id);
}
</script>

<template>
    <Head title="Projects" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Page toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Workspace</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Projects</h1>
            </div>

            <div class="flex items-center gap-3">
                <!-- Search -->
                <div class="relative hidden sm:block">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search projects..."
                        class="bg-app-elevated border border-app text-sm rounded-lg pl-9 pr-4 py-2 w-52 text-app-muted placeholder-slate-600 focus:outline-none focus:border-orange-500 transition-colors"
                    />
                </div>

                <!-- View toggle -->
                <div class="flex items-center bg-app-elevated border border-app rounded-lg p-1">
                    <button
                        @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-[var(--primary)] text-white' : 'text-app-muted hover:text-app'"
                        class="px-3 py-1.5 rounded text-xs font-medium transition-all flex items-center gap-1.5"
                    >
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        List
                    </button>
                    <button
                        @click="viewMode = 'board'"
                        :class="viewMode === 'board' ? 'bg-[var(--primary)] text-white' : 'text-app-muted hover:text-app'"
                        class="px-3 py-1.5 rounded text-xs font-medium transition-all flex items-center gap-1.5"
                    >
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10" />
                        </svg>
                        Board
                    </button>
                </div>

                <!-- New project -->
                <Link
                    href="/projects/create"
                    class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New project
                </Link>
            </div>
        </div>

        <!-- Error banner -->
        <div v-if="projectStore.error" class="mx-6 mt-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
            {{ projectStore.error }}
        </div>

        <!-- Empty state -->
        <div v-if="!projectStore.loading && projectStore.projects.length === 0" class="flex flex-col items-center justify-center flex-1 py-24 text-center">
            <div class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center mb-4">
                <svg class="size-8 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2" style="font-family: Manrope, sans-serif;">No projects yet</h3>
            <p class="text-slate-500 text-sm mb-6 max-w-xs">Create your first project to start organising work and tracking progress.</p>
            <Link href="/projects/create" class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New project
            </Link>
        </div>

        <!-- No search results -->
        <div v-else-if="filteredProjects.length === 0 && searchQuery" class="flex flex-col items-center justify-center flex-1 py-16 text-center">
            <p class="text-app-muted text-sm">No projects match <span class="text-app-muted font-medium">"{{ searchQuery }}"</span></p>
        </div>

        <!-- LIST VIEW -->
        <div v-else-if="viewMode === 'list'" class="px-6 py-5 flex flex-col gap-6">
            <template v-for="(group, key) in byStatus" :key="key">
                <div v-if="group.length > 0">
                    <div class="flex items-center gap-2 mb-3">
                        <span :class="statusDot[key]" class="inline-block size-2 rounded-full"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-app-muted">{{ statusLabel[key] }}</span>
                        <span class="ml-1 text-xs text-app-muted">{{ group.length }}</span>
                    </div>
                    <div class="rounded-xl border border-app overflow-hidden">
                        <!-- Column headers -->
                        <div class="grid grid-cols-[1fr_120px_108px] px-4 py-2 bg-app-elevated/60 border-b border-app text-xs text-app-muted font-semibold uppercase tracking-wider">
                            <span>Project</span>
                            <span class="text-right">Status</span>
                            <span class="text-right">Actions</span>
                        </div>
                        <!-- Rows -->
                        <div
                            v-for="project in group"
                            :key="project.id"
                            class="grid grid-cols-[1fr_120px_108px] items-center px-4 py-3.5 border-b last:border-0 border-app/60 hover:bg-app-elevated/30 transition-colors group"
                        >
                            <div class="flex flex-col gap-0.5 min-w-0 pr-4">
                                <Link
                                    :href="`/projects/${project.id}`"
                                    class="text-sm font-semibold text-slate-100 hover:text-[var(--primary)] transition-colors truncate"
                                >
                                    {{ project.name }}
                                </Link>
                                <p v-if="project.description" class="text-xs text-app-muted truncate">{{ project.description }}</p>
                            </div>
                            <div class="flex justify-end">
                                <span :class="statusColor[project.status]" class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full border">
                                    <span :class="statusDot[project.status]" class="size-1.5 rounded-full"></span>
                                    {{ statusLabel[project.status] }}
                                </span>
                            </div>
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Link
                                    :href="`/projects/${project.id}/edit`"
                                    class="p-1.5 rounded text-app-muted hover:text-app hover:bg-app-elevated transition-colors"
                                    title="Edit"
                                >
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>
                                <button
                                    @click="deleteProject(project.id)"
                                    class="p-1.5 rounded text-app-muted hover:text-red-400 hover:bg-red-500/10 transition-colors"
                                    title="Delete"
                                >
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <Link
                                    :href="`/projects/${project.id}`"
                                    class="p-1.5 rounded text-slate-500 hover:text-[var(--primary)] hover:bg-orange-500/10 transition-colors"
                                    title="Open"
                                >
                                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- BOARD VIEW -->
        <div v-else class="flex gap-4 px-6 py-5 overflow-x-auto min-h-[400px]">
            <template v-for="(group, key) in byStatus" :key="key">
                <div class="flex-shrink-0 w-72">
                    <div class="flex items-center gap-2 mb-3">
                        <span :class="statusDot[key]" class="size-2 rounded-full"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-app-muted">{{ statusLabel[key] }}</span>
                        <span class="ml-auto text-xs font-semibold text-app-muted bg-app-elevated px-2 py-0.5 rounded-full">{{ group.length }}</span>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div
                            v-for="project in group"
                            :key="project.id"
                            class="bg-app-surface border border-app rounded-xl p-4 hover:border-[var(--primary)]/40 hover:bg-app-elevated/50 transition-all group"
                        >
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <Link
                                    :href="`/projects/${project.id}`"
                                    class="text-sm font-semibold text-slate-100 hover:text-[var(--primary)] transition-colors leading-snug"
                                >
                                    {{ project.name }}
                                </Link>
                                <span :class="statusColor[key]" class="shrink-0 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full border">
                                    {{ statusLabel[key] }}
                                </span>
                            </div>
                            <p class="text-xs text-app-muted mb-4 line-clamp-2">{{ project.description || 'No description.' }}</p>
                            <div class="flex items-center justify-between">
                                <Link :href="`/projects/${project.id}`" class="text-xs text-[var(--primary)] font-semibold hover:underline">
                                    Open →
                                </Link>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <Link
                                        :href="`/projects/${project.id}/edit`"
                                        class="p-1 rounded text-app-muted hover:text-app hover:bg-app-elevated transition-colors"
                                    >
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <button
                                        @click="deleteProject(project.id)"
                                        class="p-1 rounded text-app-muted hover:text-red-400 hover:bg-red-500/10 transition-colors"
                                    >
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty column placeholder -->
                        <div v-if="group.length === 0" class="border border-dashed border-app rounded-xl p-6 text-center">
                            <p class="text-xs text-app-muted">No {{ statusLabel[key].toLowerCase() }} projects</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
