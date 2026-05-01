<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { show } from '@/routes/app/tasks';
import type { Task } from '@/stores/useTaskStore';

defineProps<{
    task: {
        data: Task;
    };
    logs: {
        data: Array<{
            id: number;
            action: string;
            changes: Record<string, unknown> | null;
            created_at: string;
            user?: {
                data?: {
                    name: string;
                };
            };
        }>;
    };
}>();
</script>

<template>
    <Head title="Task history" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader title="Task history" :description="task.data.title" />
            <Button variant="outline" as-child>
                <Link :href="show(task.data)">Back to task</Link>
            </Button>
        </div>

        <Card class="rounded-lg">
            <CardHeader>
                <CardTitle>Activity log</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-col gap-3">
                <div v-for="log in logs.data" :key="log.id" class="rounded-md border p-3 text-sm">
                    <div class="flex flex-col justify-between gap-1 sm:flex-row">
                        <p class="font-medium">{{ log.action }}</p>
                        <p class="text-muted-foreground">{{ log.created_at }}</p>
                    </div>
                    <pre class="mt-2 overflow-x-auto rounded bg-muted p-2 text-xs">{{ log.changes }}</pre>
                </div>

                <p v-if="logs.data.length === 0" class="text-sm text-muted-foreground">
                    No task activity has been logged yet.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
