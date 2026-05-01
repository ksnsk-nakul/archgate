<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, BriefcaseBusiness, ListChecks, Plus, UsersRound } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps<{
    stats: {
        projects: number;
        tasks: number;
        contacts: number;
        courses: number;
    };
    recentProjects: Array<{ id: number; name: string; status: string }>;
    recentTasks: Array<{ id: number; title: string; status: string; priority: string }>;
}>();

const statCards = [
    { label: 'Active projects', key: 'projects' as const, icon: BriefcaseBusiness, href: '/projects' },
    { label: 'Open tasks', key: 'tasks' as const, icon: ListChecks, href: '/tasks' },
    { label: 'CRM contacts', key: 'contacts' as const, icon: UsersRound, href: '/contacts' },
    { label: 'Courses', key: 'courses' as const, icon: BookOpen, href: '/courses' },
];

const priorityVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    high: 'destructive',
    medium: 'secondary',
    low: 'outline',
};

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    active: 'default',
    completed: 'secondary',
    archived: 'outline',
    pending: 'outline',
    in_progress: 'secondary',
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <PageHeader
            title="Dashboard"
            description="Operational overview for projects, tasks, CRM, library, and learning activity."
        />

        <!-- Stats grid -->
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <Link
                v-for="card in statCards"
                :key="card.label"
                :href="card.href"
                class="group"
            >
                <Card class="rounded-lg transition-shadow group-hover:shadow-md">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                            <component :is="card.icon" class="size-4" />
                            {{ card.label }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-3xl font-semibold">{{ props.stats[card.key] }}</p>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <!-- Recent activity grid -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Recent projects -->
            <Card class="rounded-lg">
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-base">Recent projects</CardTitle>
                    <Button size="sm" as-child>
                        <Link href="/projects/create">
                            <Plus class="size-3.5" />
                            New
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent class="flex flex-col gap-2">
                    <div
                        v-for="project in recentProjects"
                        :key="project.id"
                        class="flex items-center justify-between rounded-md border px-3 py-2 text-sm"
                    >
                        <Link :href="`/projects/${project.id}`" class="font-medium hover:underline">
                            {{ project.name }}
                        </Link>
                        <Badge :variant="statusVariant[project.status] ?? 'outline'">{{ project.status }}</Badge>
                    </div>
                    <p v-if="recentProjects.length === 0" class="text-sm text-muted-foreground">
                        No projects yet. <Link href="/projects/create" class="underline">Create one</Link>.
                    </p>
                </CardContent>
            </Card>

            <!-- Recent tasks -->
            <Card class="rounded-lg">
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="text-base">Recent tasks</CardTitle>
                    <Button size="sm" as-child>
                        <Link href="/tasks/create">
                            <Plus class="size-3.5" />
                            New
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent class="flex flex-col gap-2">
                    <div
                        v-for="task in recentTasks"
                        :key="task.id"
                        class="flex items-center justify-between rounded-md border px-3 py-2 text-sm"
                    >
                        <Link :href="`/tasks/${task.id}`" class="font-medium hover:underline">
                            {{ task.title }}
                        </Link>
                        <div class="flex items-center gap-1.5">
                            <Badge :variant="priorityVariant[task.priority] ?? 'outline'" class="text-xs">{{ task.priority }}</Badge>
                            <Badge :variant="statusVariant[task.status] ?? 'outline'" class="text-xs">{{ task.status }}</Badge>
                        </div>
                    </div>
                    <p v-if="recentTasks.length === 0" class="text-sm text-muted-foreground">
                        No tasks yet. <Link href="/tasks/create" class="underline">Create one</Link>.
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Quick actions -->
        <Card class="rounded-lg">
            <CardHeader>
                <CardTitle>Quick actions</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <Button variant="outline" as-child>
                    <Link href="/projects">View all projects</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/tasks">View all tasks</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/contacts">Open CRM</Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/courses">Browse courses</Link>
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
