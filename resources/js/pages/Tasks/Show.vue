<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { edit, history } from '@/routes/app/tasks';
import { store as storeSubtask, update as updateSubtask } from '@/routes/app/tasks/subtasks';
import type { Task } from '@/stores/useTaskStore';

type Subtask = {
    id: number;
    title: string;
    completed: boolean;
    task_id: number;
};

const props = defineProps<{
    task: {
        data: Task & {
            subtasks?: Subtask[] | { data: Subtask[] };
        };
    };
}>();

const subtasks = computed(() => {
    const value = props.task.data.subtasks;

    if (!value) {
        return [];
    }

    return Array.isArray(value) ? value : value.data;
});
</script>

<template>
    <Head title="Task details" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader title="Task details" description="Review assignment metadata, subtasks, and change history." />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link :href="history(task.data)">Task history</Link>
                </Button>
                <Button as-child>
                    <Link :href="edit(task.data)">Edit task</Link>
                </Button>
            </div>
        </div>

        <Card class="rounded-lg">
            <CardHeader>
                <CardTitle>{{ task.data.title }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-4 text-sm">
                <div class="grid gap-2 sm:grid-cols-3">
                    <div>
                        <p class="text-muted-foreground">Status</p>
                        <p class="font-medium">{{ task.data.status }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Priority</p>
                        <p class="font-medium">{{ task.data.priority }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Due date</p>
                        <p class="font-medium">{{ task.data.converted_due_date || 'Not set' }}</p>
                    </div>
                </div>
                <p class="text-muted-foreground">
                    {{ task.data.description || 'No description yet.' }}
                </p>
            </CardContent>
        </Card>

        <Card class="rounded-lg">
            <CardHeader>
                <CardTitle>Subtasks</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
                <Form
                    v-bind="storeSubtask.form(task.data)"
                    class="grid gap-3 rounded-md border p-3 md:grid-cols-[1fr_auto]"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <input
                            name="title"
                            class="h-10 rounded-md border bg-background px-3 text-sm"
                            placeholder="Add subtask"
                            required
                        />
                        <InputError :message="errors.title" />
                    </div>
                    <Button type="submit" :disabled="processing">Add subtask</Button>
                </Form>

                <div v-for="subtask in subtasks" :key="subtask.id" class="rounded-md border p-3">
                    <Form
                        v-bind="updateSubtask.form({ task: task.data.id, subtask: subtask.id })"
                        class="grid gap-3 md:grid-cols-[auto_1fr_auto]"
                    >
                        <input type="hidden" name="completed" value="0" />
                        <input
                            type="checkbox"
                            name="completed"
                            value="1"
                            :checked="subtask.completed"
                            class="mt-3 size-4"
                        />
                        <input
                            name="title"
                            :default-value="subtask.title"
                            class="h-10 rounded-md border bg-background px-3 text-sm"
                            required
                        />
                        <Button type="submit" variant="outline">Save</Button>
                    </Form>
                </div>

                <p v-if="subtasks.length === 0" class="text-sm text-muted-foreground">
                    No subtasks have been added yet.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
