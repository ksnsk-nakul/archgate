<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, ChevronRight, Plus } from 'lucide-vue-next';
import { useLearningStore } from '@/stores/useLearningStore';
import type { Course, Enrollment } from '@/stores/useLearningStore';

const learningStore = useLearningStore();

const props = defineProps<{
    courses: { data: Course[] };
    enrollments: { data: Enrollment[] };
    canCreate?: boolean;
}>();

learningStore.hydrateCourses(props.courses.data, props.enrollments.data);

function progressFor(courseId: number): number {
    return learningStore.enrollments.find((e) => e.course_id === courseId)?.progress ?? 0;
}

function isEnrolled(courseId: number): boolean {
    return learningStore.enrollments.some((e) => e.course_id === courseId);
}
</script>

<template>
    <Head title="Courses" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Learning</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Courses</h1>
            </div>
            <Link
                v-if="props.canCreate"
                href="/courses/create"
                class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
            >
                <Plus class="size-3.5" />
                New course
            </Link>
        </div>

        <div class="px-6 py-6">
            <!-- Course grid -->
            <div v-if="learningStore.courses.length > 0" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div
                    v-for="course in learningStore.courses"
                    :key="course.id"
                    class="rounded-xl border border-app bg-app-surface overflow-hidden flex flex-col hover:border-[var(--primary)]/30 transition-colors"
                >
                    <div class="px-5 py-4 border-b border-app flex items-start justify-between gap-3">
                        <div class="size-9 rounded-lg bg-purple-500/10 flex items-center justify-center shrink-0">
                            <BookOpen class="size-4 text-purple-400" />
                        </div>
                        <span
                            v-if="isEnrolled(course.id)"
                            class="text-xs font-semibold px-2 py-0.5 rounded-full border text-emerald-400 bg-emerald-500/10 border-emerald-500/20"
                        >Enrolled</span>
                    </div>

                    <div class="px-5 py-4 flex flex-col flex-1 gap-3">
                        <div>
                            <Link
                                :href="`/courses/${course.id}`"
                                class="text-sm font-bold text-app hover:text-[var(--primary)] transition-colors leading-snug"
                                style="font-family: Manrope, sans-serif;"
                            >{{ course.title }}</Link>
                            <p class="text-xs text-app-muted mt-1 line-clamp-2">{{ course.description || 'No description.' }}</p>
                        </div>

                        <div v-if="isEnrolled(course.id)" class="space-y-1.5">
                            <div class="flex justify-between text-xs text-app-muted">
                                <span>Progress</span>
                                <span>{{ progressFor(course.id) }}%</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-app-elevated overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-[var(--primary)] transition-all"
                                    :style="{ width: `${progressFor(course.id)}%` }"
                                />
                            </div>
                        </div>

                        <Link
                            :href="`/courses/${course.id}`"
                            class="mt-auto flex items-center justify-between text-xs font-semibold text-[var(--primary)] border border-[var(--primary)]/30 hover:bg-[var(--primary)]/5 px-3 py-2 rounded-lg transition-colors"
                        >
                            {{ isEnrolled(course.id) ? 'Continue course' : 'View course' }}
                            <ChevronRight class="size-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="size-14 rounded-2xl bg-purple-500/10 flex items-center justify-center mb-4">
                    <BookOpen class="size-7 text-purple-400" />
                </div>
                <h3 class="text-base font-bold text-app mb-1" style="font-family: Manrope, sans-serif;">No courses yet</h3>
                <p class="text-sm text-app-muted mb-4">Check back later — courses will appear here when published.</p>
                <Link
                    v-if="props.canCreate"
                    href="/courses/create"
                    class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-4 py-2 rounded-lg transition-colors"
                >
                    <Plus class="size-3.5" /> Create first course
                </Link>
            </div>
        </div>
    </div>
</template>
