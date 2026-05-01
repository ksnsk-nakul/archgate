<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { edit } from '@/routes/app/projects';
import type { Project } from '@/stores/useProjectStore';

defineProps<{
    project: {
        data: Project;
    };
}>();
</script>

<template>
    <Head title="Project details" />

    <div class="flex flex-col gap-6 p-4">
        <PageHeader title="Project details" description="Review project scope, task progress, and next actions." />

        <div class="rounded-lg border p-6">
            <p class="text-sm text-muted-foreground">{{ project.data.status }}</p>
            <p class="text-lg font-medium">{{ project.data.name }}</p>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ project.data.description || 'No description yet.' }}
            </p>
        </div>

        <div class="flex gap-3">
            <Button as-child>
                <Link :href="edit(project.data)">Edit project</Link>
            </Button>
            <Button variant="outline" as-child>
                <Link href="/tasks">View tasks</Link>
            </Button>
        </div>
    </div>
</template>
