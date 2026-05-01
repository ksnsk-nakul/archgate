import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { destroy as destroyTask, index as listTasks, store as storeTask } from '@/routes/tasks';
import { apiRequest } from './api';
import type { ApiCollection, ApiItem } from './api';

export type Task = {
    id: number;
    title: string;
    description?: string | null;
    status: 'pending' | 'in_progress' | 'completed';
    priority: 'low' | 'medium' | 'high';
    due_date?: string | null;
    project_id?: number;
    assignee_id?: number | null;
    converted_due_date?: string | null;
};

type TaskFilters = {
    status: 'all' | Task['status'];
    priority: 'all' | Task['priority'];
};

export const useTaskStore = defineStore('tasks', () => {
    const tasks = ref<Task[]>([]);
    const filters = ref<TaskFilters>({ status: 'all', priority: 'all' });
    const loading = ref(false);
    const error = ref<string | null>(null);

    function hydrateTasks(initialTasks: Task[]): void {
        tasks.value = initialTasks;
    }

    const filteredTasks = computed(() =>
        tasks.value.filter((task) => {
            const matchesStatus =
                filters.value.status === 'all' ||
                task.status === filters.value.status;
            const matchesPriority =
                filters.value.priority === 'all' ||
                task.priority === filters.value.priority;

            return matchesStatus && matchesPriority;
        }),
    );

    async function fetchTasks(): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiRequest<ApiCollection<Task>>(listTasks.url());
            tasks.value = response.data;
        } catch (caught) {
            error.value =
                caught instanceof Error ? caught.message : 'Unable to load tasks';
        } finally {
            loading.value = false;
        }
    }

    async function createTask(payload: Partial<Task> & { title: string }): Promise<void> {
        const optimisticTask: Task = {
            id: Date.now(),
            title: payload.title,
            description: payload.description,
            status: payload.status ?? 'pending',
            priority: payload.priority ?? 'medium',
            due_date: payload.due_date,
            converted_due_date: payload.converted_due_date,
            project_id: payload.project_id,
        };

        tasks.value = [optimisticTask, ...tasks.value];

        try {
            const response = await apiRequest<ApiItem<Task>>(storeTask.url(), {
                method: 'POST',
                body: JSON.stringify(payload),
            });
            tasks.value = tasks.value.map((task) =>
                task.id === optimisticTask.id ? response.data : task,
            );
        } catch (caught) {
            tasks.value = tasks.value.filter(
                (task) => task.id !== optimisticTask.id,
            );
            error.value =
                caught instanceof Error ? caught.message : 'Unable to create task';
        }
    }

    async function deleteTask(id: number): Promise<void> {
        const snapshot = [...tasks.value];
        tasks.value = tasks.value.filter((t) => t.id !== id);

        try {
            await apiRequest<void>(destroyTask.url(id), { method: 'DELETE' });
        } catch (caught) {
            tasks.value = snapshot;
            error.value =
                caught instanceof Error ? caught.message : 'Unable to delete task';
        }
    }

    return {
        createTask,
        deleteTask,
        error,
        fetchTasks,
        filteredTasks,
        filters,
        hydrateTasks,
        loading,
        tasks,
    };
});
