<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, BriefcaseBusiness, ListChecks, Plus, TrendingUp, UsersRound } from 'lucide-vue-next';

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
    { label: 'Active projects', key: 'projects' as const, icon: BriefcaseBusiness, href: '/projects', color: 'text-blue-400', bg: 'bg-blue-500/10' },
    { label: 'Open tasks', key: 'tasks' as const, icon: ListChecks, href: '/tasks', color: 'text-amber-400', bg: 'bg-amber-500/10' },
    { label: 'CRM contacts', key: 'contacts' as const, icon: UsersRound, href: '/contacts', color: 'text-emerald-400', bg: 'bg-emerald-500/10' },
    { label: 'Courses', key: 'courses' as const, icon: BookOpen, href: '/courses', color: 'text-purple-400', bg: 'bg-purple-500/10' },
];

const priorityColor: Record<string, string> = {
    high: 'text-red-400 bg-red-500/10 border-red-500/20',
    medium: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
    low: 'text-slate-400 bg-slate-500/10 border-slate-500/20',
};

const statusColor: Record<string, string> = {
    active: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
    completed: 'text-blue-400 bg-blue-500/10 border-blue-500/20',
    archived: 'text-slate-400 bg-slate-500/10 border-slate-500/20',
    pending: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
    in_progress: 'text-purple-400 bg-purple-500/10 border-purple-500/20',
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">Workspace</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Dashboard</h1>
            </div>
            <Link
                href="/projects/create"
                class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
            >
                <Plus class="size-3.5" />
                New project
            </Link>
        </div>

        <div class="flex flex-col gap-6 px-6 py-6">
            <!-- Stats grid -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Link
                    v-for="card in statCards"
                    :key="card.label"
                    :href="card.href"
                    class="group rounded-xl border border-app bg-app-surface p-5 hover:border-[var(--primary)]/30 transition-colors"
                >
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ card.label }}</p>
                        <div :class="[card.bg, card.color, 'size-8 rounded-lg flex items-center justify-center']">
                            <component :is="card.icon" class="size-4" />
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-app" style="font-family: Manrope, sans-serif;">{{ props.stats[card.key] }}</p>
                </Link>
            </div>

            <!-- Recent activity -->
            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Recent projects -->
                <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-app">
                        <div class="flex items-center gap-2">
                            <BriefcaseBusiness class="size-4 text-blue-400" />
                            <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Recent projects</h2>
                        </div>
                        <Link
                            href="/projects/create"
                            class="flex items-center gap-1 text-xs font-semibold text-[var(--primary)] hover:text-[var(--primary-hover)] transition-colors"
                        >
                            <Plus class="size-3.5" /> New
                        </Link>
                    </div>
                    <div class="divide-y divide-app">
                        <div
                            v-for="project in recentProjects"
                            :key="project.id"
                            class="flex items-center justify-between px-5 py-3 hover:bg-app-elevated transition-colors"
                        >
                            <Link :href="`/projects/${project.id}`" class="text-sm font-medium text-app hover:text-[var(--primary)] transition-colors">
                                {{ project.name }}
                            </Link>
                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                                :class="statusColor[project.status] ?? 'text-slate-400 bg-slate-500/10 border-slate-500/20'"
                            >{{ project.status }}</span>
                        </div>
                        <div v-if="recentProjects.length === 0" class="px-5 py-8 text-center">
                            <BriefcaseBusiness class="mx-auto mb-2 size-7 text-slate-600" />
                            <p class="text-sm text-slate-500">No projects yet.</p>
                            <Link href="/projects/create" class="text-xs text-[var(--primary)] hover:underline mt-1 inline-block">Create one</Link>
                        </div>
                    </div>
                    <div class="px-5 py-3 border-t border-app">
                        <Link href="/projects" class="text-xs font-semibold text-slate-500 hover:text-app transition-colors">View all projects →</Link>
                    </div>
                </div>

                <!-- Recent tasks -->
                <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-app">
                        <div class="flex items-center gap-2">
                            <ListChecks class="size-4 text-amber-400" />
                            <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Recent tasks</h2>
                        </div>
                        <Link
                            href="/tasks/create"
                            class="flex items-center gap-1 text-xs font-semibold text-[var(--primary)] hover:text-[var(--primary-hover)] transition-colors"
                        >
                            <Plus class="size-3.5" /> New
                        </Link>
                    </div>
                    <div class="divide-y divide-app">
                        <div
                            v-for="task in recentTasks"
                            :key="task.id"
                            class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-app-elevated transition-colors"
                        >
                            <Link :href="`/tasks/${task.id}`" class="text-sm font-medium text-app hover:text-[var(--primary)] transition-colors truncate">
                                {{ task.title }}
                            </Link>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full border" :class="priorityColor[task.priority] ?? 'text-slate-400 bg-slate-500/10 border-slate-500/20'">{{ task.priority }}</span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full border" :class="statusColor[task.status] ?? 'text-slate-400 bg-slate-500/10 border-slate-500/20'">{{ task.status }}</span>
                            </div>
                        </div>
                        <div v-if="recentTasks.length === 0" class="px-5 py-8 text-center">
                            <ListChecks class="mx-auto mb-2 size-7 text-slate-600" />
                            <p class="text-sm text-slate-500">No tasks yet.</p>
                            <Link href="/tasks/create" class="text-xs text-[var(--primary)] hover:underline mt-1 inline-block">Create one</Link>
                        </div>
                    </div>
                    <div class="px-5 py-3 border-t border-app">
                        <Link href="/tasks" class="text-xs font-semibold text-slate-500 hover:text-app transition-colors">View all tasks →</Link>
                    </div>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-5 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Quick actions</h2>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-app">
                    <Link
                        v-for="item in [
                            { label: 'All projects', href: '/projects', icon: BriefcaseBusiness, color: 'text-blue-400' },
                            { label: 'All tasks', href: '/tasks', icon: ListChecks, color: 'text-amber-400' },
                            { label: 'CRM contacts', href: '/contacts', icon: UsersRound, color: 'text-emerald-400' },
                            { label: 'Browse courses', href: '/courses', icon: BookOpen, color: 'text-purple-400' },
                        ]"
                        :key="item.label"
                        :href="item.href"
                        class="flex items-center gap-3 px-5 py-4 hover:bg-app-elevated transition-colors group"
                    >
                        <component :is="item.icon" :class="[item.color, 'size-4']" />
                        <span class="text-sm font-medium text-slate-500 group-hover:text-app transition-colors">{{ item.label }}</span>
                        <TrendingUp class="size-3.5 text-slate-700 group-hover:text-slate-500 ml-auto transition-colors" />
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
