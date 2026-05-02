<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { create, edit, show } from '@/routes/app/tasks';
import { useTaskStore } from '@/stores/useTaskStore';
import type { Task } from '@/stores/useTaskStore';

const props = defineProps<{
    tasks: {
        data: Task[];
    };
}>();

const taskStore = useTaskStore();
taskStore.hydrateTasks(props.tasks.data);

type ViewMode = 'list' | 'board';
const viewMode = ref<ViewMode>('list');
const searchQuery = ref('');
const selectedTask = ref<Task | null>(null);
const collapsedSections = ref<Set<string>>(new Set());

const priorityColor: Record<string, string> = {
    high: 'bg-red-500/15 text-red-400 border-red-500/25',
    medium: 'bg-amber-500/15 text-amber-400 border-amber-500/25',
    low: 'bg-slate-500/15 text-app-muted border-slate-500/25',
};

const statusColor: Record<string, string> = {
    pending: 'bg-slate-500/15 text-app-muted border-slate-500/25',
    in_progress: 'bg-blue-500/15 text-blue-400 border-blue-500/25',
    completed: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25',
};

const statusLabel: Record<string, string> = {
    pending: 'To do',
    in_progress: 'In progress',
    completed: 'Done',
};

const sections = ['pending', 'in_progress', 'completed'] as const;

const filteredTasks = computed(() => {
    const q = searchQuery.value.toLowerCase();

    return taskStore.tasks.filter(
        (t) => !q || t.title.toLowerCase().includes(q) || (t.description ?? '').toLowerCase().includes(q),
    );
});

const bySection = computed(() => ({
    pending: filteredTasks.value.filter((t) => t.status === 'pending'),
    in_progress: filteredTasks.value.filter((t) => t.status === 'in_progress'),
    completed: filteredTasks.value.filter((t) => t.status === 'completed'),
}));

function toggleSection(section: string): void {
    if (collapsedSections.value.has(section)) {
        collapsedSections.value.delete(section);
    } else {
        collapsedSections.value.add(section);
    }
}

function openDetail(task: Task): void {
    selectedTask.value = task;
}

function closeDetail(): void {
    selectedTask.value = null;
}

function isOverdue(dueDate: string | null | undefined): boolean {
    if (!dueDate) {
        return false;
    }

    return new Date(dueDate) < new Date();
}

async function deleteTask(id: number): Promise<void> {
    if (!confirm('Delete this task?')) {
        return;
    }

    if (selectedTask.value?.id === id) {
        closeDetail();
    }

    await taskStore.deleteTask(id);
}
</script>

<template>
    <Head title="Tasks" />

    <div class="flex min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Main content area -->
        <div class="flex flex-col flex-1 min-w-0">
            <!-- Toolbar -->
            <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
                <div>
                    <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Workspace</p>
                    <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">My Tasks</h1>
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
                            placeholder="Search tasks..."
                            class="bg-app-elevated border border-app text-sm rounded-lg pl-9 pr-4 py-2 w-48 text-app-muted placeholder-slate-600 focus:outline-none focus:border-orange-500 transition-colors"
                        />
                    </div>

                    <!-- Priority filter -->
                    <select
                        v-model="taskStore.filters.priority"
                        class="bg-app-elevated border border-app text-app-muted text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-orange-500 transition-colors"
                    >
                        <option value="all">All priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>

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

                    <!-- New task -->
                    <Link
                        :href="create()"
                        class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
                    >
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New task
                    </Link>
                </div>
            </div>

            <!-- Error -->
            <div v-if="taskStore.error" class="mx-6 mt-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                {{ taskStore.error }}
            </div>

            <!-- Empty state -->
            <div v-if="!taskStore.loading && taskStore.tasks.length === 0" class="flex flex-col items-center justify-center flex-1 py-24 text-center">
                <div class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center mb-4">
                    <svg class="size-8 text-[var(--primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-app mb-2" style="font-family: Manrope, sans-serif;">No tasks yet</h3>
                <p class="text-app-muted text-sm mb-6 max-w-xs">Create your first task to start tracking work and staying on top of your priorities.</p>
                <Link :href="create()" class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New task
                </Link>
            </div>

            <!-- No search results -->
            <div v-else-if="filteredTasks.length === 0 && (searchQuery || taskStore.filters.priority !== 'all')" class="flex flex-col items-center justify-center flex-1 py-16 text-center">
                <p class="text-app-muted text-sm">No tasks match the current filters.</p>
            </div>

            <!-- LIST VIEW -->
            <div v-else-if="viewMode === 'list'" class="px-6 py-5 flex flex-col gap-2">
                <template v-for="section in sections" :key="section">
                    <div class="mb-1">
                        <!-- Section header -->
                        <button
                            @click="toggleSection(section)"
                            class="w-full flex items-center gap-2 py-2 text-left group"
                        >
                            <svg
                                :class="collapsedSections.has(section) ? '-rotate-90' : ''"
                                class="size-3.5 text-app-muted group-hover:text-app-muted transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <span class="text-xs font-bold uppercase tracking-widest text-app-muted group-hover:text-app-muted transition-colors">
                                {{ statusLabel[section] }}
                            </span>
                            <span class="text-xs text-app-muted font-medium">{{ bySection[section].length }}</span>
                        </button>

                        <!-- Task rows -->
                        <div v-if="!collapsedSections.has(section)" class="rounded-xl border border-app overflow-hidden">
                            <!-- Column headers -->
                            <div class="hidden sm:grid grid-cols-[auto_1fr_100px_100px_90px] px-4 py-2 bg-app-elevated/60 border-b border-app text-xs text-app-muted font-semibold uppercase tracking-wider gap-3">
                                <span class="w-5"></span>
                                <span>Task</span>
                                <span class="text-right">Priority</span>
                                <span class="text-right">Due date</span>
                                <span class="text-right">Actions</span>
                            </div>

                            <div v-if="bySection[section].length === 0" class="px-4 py-6 text-center">
                                <p class="text-xs text-app-muted">No {{ statusLabel[section].toLowerCase() }} tasks</p>
                            </div>

                            <div
                                v-for="task in bySection[section]"
                                :key="task.id"
                                class="grid grid-cols-[auto_1fr] sm:grid-cols-[auto_1fr_100px_100px_90px] items-center px-4 py-3 border-b last:border-0 border-app/60 hover:bg-app-elevated/25 transition-colors group gap-3 cursor-pointer"
                                @click="openDetail(task)"
                            >
                                <!-- Completion checkbox -->
                                <div class="w-5 flex items-center" @click.stop>
                                    <div
                                        :class="task.status === 'completed' ? 'bg-emerald-500 border-emerald-500' : 'border-app hover:border-[var(--primary)]'"
                                        class="size-4 rounded border-2 flex items-center justify-center transition-colors cursor-pointer"
                                    >
                                        <svg v-if="task.status === 'completed'" class="size-2.5 text-app" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Title & description -->
                                <div class="flex flex-col gap-0.5 min-w-0">
                                    <span
                                        :class="task.status === 'completed' ? 'line-through text-app-muted' : 'text-slate-100'"
                                        class="text-sm font-medium truncate"
                                    >{{ task.title }}</span>
                                    <span v-if="task.description" class="text-xs text-app-muted truncate">{{ task.description }}</span>
                                </div>

                                <!-- Priority -->
                                <div class="hidden sm:flex justify-end">
                                    <span :class="priorityColor[task.priority]" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border">
                                        {{ task.priority }}
                                    </span>
                                </div>

                                <!-- Due date -->
                                <div class="hidden sm:flex justify-end">
                                    <span
                                        v-if="task.due_date"
                                        :class="isOverdue(task.due_date) && task.status !== 'completed' ? 'text-red-400' : 'text-app-muted'"
                                        class="text-xs"
                                    >
                                        {{ task.due_date }}
                                    </span>
                                    <span v-else class="text-xs text-app-muted">—</span>
                                </div>

                                <!-- Row actions -->
                                <div class="hidden sm:flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                                    <Link :href="edit(task)" class="p-1.5 rounded text-app-muted hover:text-app hover:bg-app-elevated transition-colors" title="Edit">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <button @click="deleteTask(task.id)" class="p-1.5 rounded text-app-muted hover:text-red-400 hover:bg-red-500/10 transition-colors" title="Delete">
                                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- BOARD VIEW -->
            <div v-else class="flex gap-4 px-6 py-5 overflow-x-auto min-h-[400px]">
                <template v-for="section in sections" :key="section">
                    <div class="flex-shrink-0 w-72">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-bold uppercase tracking-widest text-app-muted">{{ statusLabel[section] }}</span>
                            <span class="ml-auto text-xs font-semibold text-app-muted bg-app-elevated px-2 py-0.5 rounded-full">{{ bySection[section].length }}</span>
                        </div>
                        <div class="flex flex-col gap-3">
                            <div
                                v-for="task in bySection[section]"
                                :key="task.id"
                                class="bg-[#122131] border border-[#273647] rounded-xl p-4 hover:border-orange-500/40 hover:bg-[#1c2b3c] transition-all group cursor-pointer"
                                @click="openDetail(task)"
                            >
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <span class="text-sm font-semibold text-slate-100 leading-snug">{{ task.title }}</span>
                                    <span :class="priorityColor[task.priority]" class="shrink-0 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full border">
                                        {{ task.priority }}
                                    </span>
                                </div>
                                <p v-if="task.description" class="text-xs text-app-muted mb-3 line-clamp-2">{{ task.description }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span
                                        v-if="task.due_date"
                                        :class="isOverdue(task.due_date) && task.status !== 'completed' ? 'text-red-400' : 'text-app-muted'"
                                        class="text-xs flex items-center gap-1"
                                    >
                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ task.due_date }}
                                    </span>
                                    <span v-else class="text-xs text-app-muted">No due date</span>
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                                        <Link :href="edit(task)" class="p-1 rounded text-app-muted hover:text-app hover:bg-app-elevated transition-colors">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>
                                        <button @click="deleteTask(task.id)" class="p-1 rounded text-app-muted hover:text-red-400 hover:bg-red-500/10 transition-colors">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="bySection[section].length === 0" class="border border-dashed border-app rounded-xl p-6 text-center">
                                <p class="text-xs text-app-muted">No {{ statusLabel[section].toLowerCase() }} tasks</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Task detail panel (slide-in) -->
        <transition name="slide">
            <div
                v-if="selectedTask"
                class="w-80 flex-shrink-0 border-l border-app bg-app-surface flex flex-col"
            >
                <!-- Panel header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Task detail</h2>
                    <button @click="closeDetail" class="p-1.5 rounded text-app-muted hover:text-app hover:bg-app-elevated transition-colors">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Panel body -->
                <div class="flex flex-col gap-5 px-5 py-5 overflow-y-auto flex-1">
                    <div>
                        <h3 class="text-base font-bold text-app mb-1">{{ selectedTask.title }}</h3>
                        <p class="text-sm text-app-muted">{{ selectedTask.description || 'No description.' }}</p>
                    </div>

                    <div class="flex flex-col gap-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-app-muted text-xs uppercase tracking-wider font-semibold">Status</span>
                            <span :class="statusColor[selectedTask.status]" class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full border">
                                {{ statusLabel[selectedTask.status] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-app-muted text-xs uppercase tracking-wider font-semibold">Priority</span>
                            <span :class="priorityColor[selectedTask.priority]" class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full border">
                                {{ selectedTask.priority }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-app-muted text-xs uppercase tracking-wider font-semibold">Due date</span>
                            <span
                                :class="isOverdue(selectedTask.due_date) && selectedTask.status !== 'completed' ? 'text-red-400' : 'text-app-muted'"
                                class="text-xs font-medium"
                            >
                                {{ selectedTask.due_date || 'Not set' }}
                            </span>
                        </div>
                    </div>

                    <div class="h-px bg-app-elevated"></div>

                    <!-- Panel actions -->
                    <div class="flex flex-col gap-2">
                        <Link
                            :href="show(selectedTask)"
                            class="flex items-center gap-2 w-full text-sm text-app-muted hover:text-white hover:bg-app-elevated px-3 py-2 rounded-lg transition-colors"
                        >
                            <svg class="size-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View full detail
                        </Link>
                        <Link
                            :href="edit(selectedTask)"
                            class="flex items-center gap-2 w-full text-sm text-app-muted hover:text-white hover:bg-app-elevated px-3 py-2 rounded-lg transition-colors"
                        >
                            <svg class="size-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit task
                        </Link>
                        <button
                            @click="deleteTask(selectedTask.id)"
                            class="flex items-center gap-2 w-full text-sm text-red-500 hover:text-red-400 hover:bg-red-500/10 px-3 py-2 rounded-lg transition-colors"
                        >
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete task
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.2s ease;
}
.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
</style>
