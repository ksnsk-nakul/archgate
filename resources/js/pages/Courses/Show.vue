<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, ChevronDown, ChevronRight, PlayCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import { index as coursesIndex } from '@/routes/app/courses';

type Lesson = {
    id: number;
    title: string;
    type?: string | null;
    order: number;
};

type Section = {
    id: number;
    title: string;
    order: number;
    lessons: { data: Lesson[] } | Lesson[];
};

type Course = {
    id: number;
    title: string;
    description?: string | null;
    status?: string | null;
};

type Enrollment = {
    id: number;
    course_id: number;
    progress: number;
    status: string;
} | null;

const props = defineProps<{
    course: { data: Course };
    sections: { data: Section[] };
    enrollment: Enrollment;
}>();

const expandedSections = ref<Set<number>>(new Set(props.sections.data.map((s) => s.id)));

function toggleSection(id: number): void {
    if (expandedSections.value.has(id)) {
        expandedSections.value.delete(id);
    } else {
        expandedSections.value.add(id);
    }
}

function lessonsFor(section: Section): Lesson[] {
    const val = section.lessons;
    if (!val) { return []; }
    return Array.isArray(val) ? val : (val as { data: Lesson[] }).data;
}

function totalLessons(): number {
    return props.sections.data.reduce((sum, s) => sum + lessonsFor(s).length, 0);
}

const enrollForm = useForm({ course_id: props.course.data.id });

function enroll(): void {
    enrollForm.post('/api/v1/enrollments', {
        onSuccess: () => window.location.reload(),
    });
}

const statusColor: Record<string, string> = {
    published: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
    draft: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
    archived: 'text-app-muted bg-app-elevated border-app',
};
</script>

<template>
    <Head :title="course.data.title" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center gap-3 px-6 py-4 border-b border-app">
            <Link
                :href="coursesIndex()"
                class="flex items-center gap-1.5 text-xs font-semibold text-app-muted hover:text-app border border-app hover:border-app px-3 py-2 rounded-lg transition-colors"
            >
                <ArrowLeft class="size-3.5" /> Courses
            </Link>
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Courses</p>
                <h1 class="text-xl font-bold text-app truncate max-w-md" style="font-family: Manrope, sans-serif;">{{ course.data.title }}</h1>
            </div>
        </div>

        <div class="px-6 py-6 grid gap-4 lg:grid-cols-3">
            <!-- Course meta + enrollment -->
            <div class="flex flex-col gap-4 lg:col-span-1">
                <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-5 py-4 border-b border-app">
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Course info</h2>
                    </div>
                    <div class="px-5 py-4 flex flex-col gap-4">
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                                :class="statusColor[course.data.status ?? 'draft'] ?? 'text-app-muted bg-app-elevated border-app'"
                            >
                                {{ course.data.status ?? 'draft' }}
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated">
                                {{ totalLessons() }} lessons
                            </span>
                        </div>
                        <p class="text-sm text-app-muted leading-relaxed">
                            {{ course.data.description || 'No description provided.' }}
                        </p>

                        <div v-if="enrollment">
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="text-app-muted">Progress</span>
                                <span class="font-semibold text-app">{{ enrollment.progress }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-app-elevated">
                                <div
                                    class="h-full rounded-full bg-[var(--primary)] transition-all"
                                    :style="{ width: `${enrollment.progress}%` }"
                                />
                            </div>
                            <span class="mt-3 inline-block text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated">
                                {{ enrollment.status }}
                            </span>
                        </div>

                        <button
                            v-else
                            :disabled="enrollForm.processing"
                            class="w-full flex items-center justify-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors"
                            @click="enroll"
                        >
                            <svg v-if="enrollForm.processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Enroll in course
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sections + lessons -->
            <div class="flex flex-col gap-3 lg:col-span-2">
                <div v-if="sections.data.length === 0" class="rounded-xl border border-app bg-app-surface px-6 py-12 text-center">
                    <BookOpen class="mx-auto mb-2 size-7 text-app-muted" />
                    <p class="text-sm text-app-muted">No sections have been added yet.</p>
                </div>

                <div
                    v-for="section in sections.data"
                    :key="section.id"
                    class="rounded-xl border border-app bg-app-surface overflow-hidden"
                >
                    <button
                        type="button"
                        class="w-full flex items-center justify-between gap-3 px-5 py-4 border-b border-app hover:bg-app-elevated transition-colors cursor-pointer select-none"
                        @click="toggleSection(section.id)"
                    >
                        <div class="flex items-center gap-2">
                            <BookOpen class="size-4 text-app-muted" />
                            <span class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">{{ section.title }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated">
                                {{ lessonsFor(section).length }}
                            </span>
                            <ChevronDown v-if="expandedSections.has(section.id)" class="size-4 text-app-muted" />
                            <ChevronRight v-else class="size-4 text-app-muted" />
                        </div>
                    </button>

                    <div v-if="expandedSections.has(section.id)" class="divide-y divide-app">
                        <Link
                            v-for="lesson in lessonsFor(section)"
                            :key="lesson.id"
                            :href="`/lessons/${lesson.id}`"
                            class="flex items-center gap-3 px-5 py-3 text-sm hover:bg-app-elevated transition-colors"
                        >
                            <PlayCircle class="size-4 shrink-0 text-app-muted" />
                            <span class="flex-1 text-app">{{ lesson.title }}</span>
                            <span v-if="lesson.type" class="text-xs font-semibold px-2 py-0.5 rounded-full border border-app text-app-muted bg-app-elevated">
                                {{ lesson.type }}
                            </span>
                        </Link>
                        <div v-if="lessonsFor(section).length === 0" class="px-5 py-4">
                            <p class="text-sm text-app-muted">No lessons in this section yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
