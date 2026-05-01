<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/app/projects';
import type { Project } from '@/stores/useProjectStore';

defineProps<{
    project: {
        data: Project;
    };
}>();
</script>

<template>
    <Head title="Edit project" />

    <Form
        v-bind="update.form(project.data)"
        class="flex max-w-2xl flex-col gap-6 p-4"
        v-slot="{ errors, processing }"
    >
        <PageHeader title="Edit project" description="Update status and project summary." />

        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" name="name" :default-value="project.data.name" required />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="description">Description</Label>
            <Input id="description" name="description" :default-value="project.data.description ?? ''" />
            <InputError :message="errors.description" />
        </div>

        <div class="grid gap-2">
            <Label for="status">Status</Label>
            <select id="status" name="status" class="h-10 rounded-md border bg-background px-3 text-sm">
                <option value="active" :selected="project.data.status === 'active'">Active</option>
                <option value="completed" :selected="project.data.status === 'completed'">Completed</option>
                <option value="archived" :selected="project.data.status === 'archived'">Archived</option>
            </select>
            <InputError :message="errors.status" />
        </div>

        <div class="flex gap-3">
            <Button type="submit" :disabled="processing">Save project</Button>
            <Button variant="outline" type="button" @click="router.visit(`/projects/${project.data.id}`)">
                Cancel
            </Button>
        </div>
    </Form>
</template>
