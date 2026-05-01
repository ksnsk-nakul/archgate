<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/app/tasks';
import type { Project } from '@/stores/useProjectStore';
import type { Task } from '@/stores/useTaskStore';
import type { User } from '@/types/auth';

const props = defineProps<{
    task: {
        data: Task;
    };
    projects: {
        data: Project[];
    };
    users: {
        data: User[];
    };
}>();
</script>

<template>
    <Head title="Edit task" />

    <Form
        v-bind="update.form(task.data)"
        class="flex max-w-3xl flex-col gap-6 p-4"
        v-slot="{ errors, processing }"
    >
        <PageHeader title="Edit task" description="Update task details and append an activity log entry." />

        <div class="grid gap-2">
            <Label for="title">Title</Label>
            <Input id="title" name="title" :default-value="props.task.data.title" required />
            <InputError :message="errors.title" />
        </div>

        <div class="grid gap-2">
            <Label for="description">Description</Label>
            <Input id="description" name="description" :default-value="props.task.data.description ?? ''" />
            <InputError :message="errors.description" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="project_id">Project</Label>
                <select id="project_id" name="project_id" required class="h-10 rounded-md border bg-background px-3 text-sm">
                    <option v-for="project in projects.data" :key="project.id" :value="project.id" :selected="project.id === props.task.data.project_id">
                        {{ project.name }}
                    </option>
                </select>
                <InputError :message="errors.project_id" />
            </div>

            <div class="grid gap-2">
                <Label for="assignee_id">Assignee</Label>
                <select id="assignee_id" name="assignee_id" class="h-10 rounded-md border bg-background px-3 text-sm">
                    <option value="">Unassigned</option>
                    <option v-for="user in users.data" :key="user.id" :value="user.id" :selected="user.id === props.task.data.assignee_id">
                        {{ user.name }}
                    </option>
                </select>
                <InputError :message="errors.assignee_id" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label for="status">Status</Label>
                <select id="status" name="status" class="h-10 rounded-md border bg-background px-3 text-sm">
                    <option value="pending" :selected="props.task.data.status === 'pending'">Pending</option>
                    <option value="in_progress" :selected="props.task.data.status === 'in_progress'">In progress</option>
                    <option value="completed" :selected="props.task.data.status === 'completed'">Completed</option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="priority">Priority</Label>
                <select id="priority" name="priority" class="h-10 rounded-md border bg-background px-3 text-sm">
                    <option value="medium" :selected="props.task.data.priority === 'medium'">Medium</option>
                    <option value="low" :selected="props.task.data.priority === 'low'">Low</option>
                    <option value="high" :selected="props.task.data.priority === 'high'">High</option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="due_date">Due date</Label>
                <Input id="due_date" name="due_date" type="date" :default-value="String(props.task.data.due_date ?? '').slice(0, 10)" />
            </div>
        </div>

        <div class="flex gap-3">
            <Button type="submit" :disabled="processing">Save task</Button>
            <Button variant="outline" type="button" @click="router.visit(`/tasks/${props.task.data.id}`)">
                Cancel
            </Button>
        </div>
    </Form>
</template>
