<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/app/tasks';
import type { Project } from '@/stores/useProjectStore';
import type { User } from '@/types/auth';

defineProps<{
    projects: {
        data: Project[];
    };
    users: {
        data: User[];
    };
}>();
</script>

<template>
    <Head title="Create task" />

    <Form
        v-bind="store.form()"
        class="flex max-w-3xl flex-col gap-6 p-4"
        v-slot="{ errors, processing }"
    >
        <PageHeader title="Create task" description="Create a task and record the activity automatically." />

        <div class="grid gap-2">
            <Label for="title">Title</Label>
            <Input id="title" name="title" required />
            <InputError :message="errors.title" />
        </div>

        <div class="grid gap-2">
            <Label for="description">Description</Label>
            <Input id="description" name="description" />
            <InputError :message="errors.description" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="project_id">Project</Label>
                <select id="project_id" name="project_id" required class="h-10 rounded-md border bg-background px-3 text-sm">
                    <option value="">Select project</option>
                    <option v-for="project in projects.data" :key="project.id" :value="project.id">
                        {{ project.name }}
                    </option>
                </select>
                <InputError :message="errors.project_id" />
            </div>

            <div class="grid gap-2">
                <Label for="assignee_id">Assignee</Label>
                <select id="assignee_id" name="assignee_id" class="h-10 rounded-md border bg-background px-3 text-sm">
                    <option value="">Unassigned</option>
                    <option v-for="user in users.data" :key="user.id" :value="user.id">
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
                    <option value="pending">Pending</option>
                    <option value="in_progress">In progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="priority">Priority</Label>
                <select id="priority" name="priority" class="h-10 rounded-md border bg-background px-3 text-sm">
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                    <option value="high">High</option>
                </select>
            </div>

            <div class="grid gap-2">
                <Label for="due_date">Due date</Label>
                <Input id="due_date" name="due_date" type="date" />
            </div>
        </div>

        <div class="flex gap-3">
            <Button type="submit" :disabled="processing">Create task</Button>
            <Button variant="outline" type="button" @click="router.visit('/tasks')">
                Cancel
            </Button>
        </div>
    </Form>
</template>
