<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ClipboardList, History, Pencil, Plus } from 'lucide-vue-next';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { edit, history } from '@/routes/app/tasks';
import { store as storeSubtask, update as updateSubtask } from '@/routes/app/tasks/subtasks';
import type { Task } from '@/stores/useTaskStore';

type Subtask = { id: number; title: string; completed: boolean; task_id: number };

const props = defineProps<{
    task: { data: Task & { subtasks?: Subtask[] | { data: Subtask[] } } };
}>();

const subtasks = computed(() => {
    const value = props.task.data.subtasks;
    if (!value) { return []; }
    return Array.isArray(value) ? value : value.data;
});

const priorityColor: Record<string, string> = {
    high: 'text-red-400 bg-red-500/10 border-red-500/20',
    medium: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
    low: 'text-app-muted bg-app-elevated border-app',
};
const statusColor: Record<string, string> = {
    pending: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
    in_progress: 'text-purple-400 bg-purple-500/10 border-purple-500/20',
    completed: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
};
</script>

<template>
    <Head title="Task details" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 md:px-6 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Tasks</p>
                <h1 class="text-xl font-bold text-app truncate max-w-md" style="font-family: Manrope, sans-serif;">{{ task.data.title }}</h1>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <Link
                    :href="history(task.data)"
                    class="flex items-center gap-1.5 text-xs font-semibold text-app-muted border border-app hover:text-app hover:border-app px-3 py-2 rounded-lg transition-colors"
                >
                    <History class="size-3.5" /> History
                </Link>
                <Link
                    :href="edit(task.data)"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
                >
                    <Pencil class="size-3.5" /> Edit
                </Link>
            </div>
        </div>

        <div class="px-4 py-6 md:px-6 flex flex-col gap-5 max-w-3xl">
            <!-- Task meta -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app flex items-center gap-2">
                    <ClipboardList class="size-4 text-amber-400" />
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Task details</h2>
                </div>
                <div class="px-6 py-5 flex flex-col gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Status</p>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border" :class="statusColor[task.data.status] ?? 'text-app-muted bg-app-elevated border-app'">
                                {{ task.data.status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Priority</p>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border" :class="priorityColor[task.data.priority] ?? 'text-app-muted bg-app-elevated border-app'">
                                {{ task.data.priority }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Due date</p>
                            <p class="text-sm font-medium text-app">{{ task.data.converted_due_date || '—' }}</p>
                        </div>
                    </div>
                    <div v-if="task.data.description">
                        <p class="text-xs text-app-muted uppercase tracking-wider font-semibold mb-1">Description</p>
                        <p class="text-sm text-app-muted leading-relaxed">{{ task.data.description }}</p>
                    </div>
                </div>
            </div>

            <!-- Subtasks -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app flex items-center gap-2">
                    <Plus class="size-4 text-[var(--primary)]" />
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Subtasks</h2>
                    <span class="ml-auto text-xs text-app-muted">{{ subtasks.length }} item{{ subtasks.length !== 1 ? 's' : '' }}</span>
                </div>

                <div class="px-6 py-5 flex flex-col gap-3">
                    <!-- Add subtask -->
                    <Form
                        v-bind="storeSubtask.form(task.data)"
                        class="flex gap-2"
                        v-slot="{ errors, processing }"
                    >
                        <div class="flex-1">
                            <input
                                name="title"
                                class="input-app w-full rounded-lg px-3 py-2 text-sm border transition-colors"
                                placeholder="Add a subtask…"
                                required
                            />
                            <InputError :message="errors.title" />
                        </div>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 text-white px-3 py-2 rounded-lg transition-colors shrink-0"
                        >
                            <Plus class="size-3.5" /> Add
                        </button>
                    </Form>

                    <!-- Subtask list -->
                    <div v-if="subtasks.length > 0" class="flex flex-col gap-2 mt-1">
                        <Form
                            v-for="subtask in subtasks"
                            :key="subtask.id"
                            v-bind="updateSubtask.form({ task: task.data.id, subtask: subtask.id })"
                            class="flex items-center gap-3 rounded-lg border border-app bg-app-elevated px-3 py-2"
                        >
                            <input type="hidden" name="completed" value="0" />
                            <input
                                type="checkbox"
                                name="completed"
                                value="1"
                                :checked="subtask.completed"
                                class="size-4 rounded accent-[var(--primary)] shrink-0"
                            />
                            <input
                                name="title"
                                :default-value="subtask.title"
                                class="flex-1 bg-transparent text-sm text-app focus:outline-none"
                                required
                            />
                            <button type="submit" class="text-xs font-semibold text-[var(--primary)] hover:text-[var(--primary-hover)] shrink-0 transition-colors">Save</button>
                        </Form>
                    </div>
                    <p v-else class="text-sm text-app-muted italic">No subtasks yet.</p>
                </div>
            </div>
        </div>
    </div>
</template>
