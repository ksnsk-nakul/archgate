<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, BookOpen, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';

type Lesson = {
    id: number;
    title: string;
    content?: string | null;
    type?: string | null;
    order: number;
    section_id: number;
};

type SectionLesson = {
    id: number;
    title: string;
    type?: string | null;
    order: number;
};

type Section = {
    id: number;
    title: string;
    order: number;
    lessons: { data: SectionLesson[] } | SectionLesson[];
};

type Course = {
    id: number;
    title: string;
};

type Enrollment = {
    id: number;
    progress: number;
    status: string;
} | null;

const props = defineProps<{
    lesson: { data: Lesson };
    course: { data: Course };
    sections: { data: Section[] };
    enrollment: Enrollment;
}>();

function lessonsFor(section: Section): SectionLesson[] {
    const val = section.lessons;
    if (!val) { return []; }
    return Array.isArray(val) ? val : (val as { data: SectionLesson[] }).data;
}

const allLessons = computed<SectionLesson[]>(() =>
    props.sections.data.flatMap((s) => lessonsFor(s)),
);

const currentIndex = computed(() =>
    allLessons.value.findIndex((l) => l.id === props.lesson.data.id),
);

const prevLesson = computed(() =>
    currentIndex.value > 0 ? allLessons.value[currentIndex.value - 1] : null,
);

const nextLesson = computed(() =>
    currentIndex.value < allLessons.value.length - 1
        ? allLessons.value[currentIndex.value + 1]
        : null,
);

const currentSection = computed(() =>
    props.sections.data.find((s) =>
        lessonsFor(s).some((l) => l.id === props.lesson.data.id),
    ),
);

const typeColor: Record<string, string> = {
    video: 'text-purple-400 bg-purple-500/10 border-purple-500/20',
    article: 'text-blue-400 bg-blue-500/10 border-blue-500/20',
    quiz: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
};
</script>

<template>
    <Head :title="lesson.data.title" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Breadcrumb toolbar -->
        <div class="flex items-center gap-2 px-4 py-4 md:px-6 border-b border-app text-sm overflow-x-auto">
            <Link :href="`/courses/${course.data.id}`" class="text-app-muted hover:text-app transition-colors font-semibold">
                {{ course.data.title }}
            </Link>
            <ChevronRight class="size-3.5 text-app-muted" />
            <span v-if="currentSection" class="text-app-muted">{{ currentSection.title }}</span>
            <ChevronRight v-if="currentSection" class="size-3.5 text-app-muted" />
            <span class="text-app font-semibold truncate max-w-xs">{{ lesson.data.title }}</span>
        </div>

        <div class="px-4 py-6 md:px-6 grid gap-4 lg:grid-cols-4">
            <!-- Lesson content -->
            <div class="flex flex-col gap-4 lg:col-span-3">
                <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                        <h1 class="text-lg font-bold text-app flex-1" style="font-family: Manrope, sans-serif;">{{ lesson.data.title }}</h1>
                        <span
                            v-if="lesson.data.type"
                            class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                            :class="typeColor[lesson.data.type] ?? 'text-app-muted bg-app-elevated border-app'"
                        >
                            {{ lesson.data.type }}
                        </span>
                    </div>
                    <div class="px-6 py-5">
                        <p v-if="lesson.data.content" class="text-sm text-app-muted leading-relaxed whitespace-pre-wrap">
                            {{ lesson.data.content }}
                        </p>
                        <p v-else class="text-sm text-app-muted italic">
                            No content has been added to this lesson yet.
                        </p>
                    </div>
                </div>

                <!-- Prev / Next navigation -->
                <div class="flex items-center justify-between gap-4">
                    <Link
                        v-if="prevLesson"
                        :href="`/lessons/${prevLesson.id}`"
                        class="flex items-center gap-1.5 text-xs font-semibold text-app-muted hover:text-app border border-app hover:border-app px-3 py-2 rounded-lg transition-colors"
                    >
                        <ArrowLeft class="size-3.5" /> {{ prevLesson.title }}
                    </Link>
                    <div v-else />

                    <Link
                        v-if="nextLesson"
                        :href="`/lessons/${nextLesson.id}`"
                        class="flex items-center gap-1.5 text-xs font-semibold bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white px-3 py-2 rounded-lg transition-colors"
                    >
                        {{ nextLesson.title }} <ArrowRight class="size-3.5" />
                    </Link>
                    <Link
                        v-else
                        :href="`/courses/${course.data.id}`"
                        class="flex items-center gap-1.5 text-xs font-semibold text-app-muted hover:text-app border border-app hover:border-app px-3 py-2 rounded-lg transition-colors"
                    >
                        Back to course
                    </Link>
                </div>
            </div>

            <!-- Sidebar: course outline -->
            <div class="flex flex-col gap-3 lg:col-span-1">
                <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-4 py-3 border-b border-app flex items-center gap-2">
                        <BookOpen class="size-4 text-app-muted" />
                        <h2 class="text-xs font-bold text-app uppercase tracking-wider" style="font-family: Manrope, sans-serif;">Course outline</h2>
                    </div>
                    <div class="px-3 py-3 flex flex-col gap-3">
                        <div v-for="section in sections.data" :key="section.id">
                            <p class="mb-1 px-2 text-xs font-semibold uppercase tracking-wider text-app-muted">
                                {{ section.title }}
                            </p>
                            <div class="flex flex-col gap-0.5">
                                <Link
                                    v-for="l in lessonsFor(section)"
                                    :key="l.id"
                                    :href="`/lessons/${l.id}`"
                                    class="rounded-lg px-2 py-1.5 text-xs transition-colors"
                                    :class="l.id === lesson.data.id
                                        ? 'bg-[var(--primary)]/10 text-[var(--primary)] font-semibold'
                                        : 'text-app-muted hover:text-app hover:bg-app-elevated'"
                                >
                                    {{ l.title }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress card if enrolled -->
                <div v-if="enrollment" class="rounded-xl border border-app bg-app-surface overflow-hidden">
                    <div class="px-4 py-3 border-b border-app">
                        <h2 class="text-xs font-bold text-app uppercase tracking-wider">Progress</h2>
                    </div>
                    <div class="px-4 py-3">
                        <div class="mb-2 flex justify-between text-sm">
                            <span class="text-app-muted">{{ enrollment.status }}</span>
                            <span class="font-semibold text-app">{{ enrollment.progress }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-app-elevated">
                            <div
                                class="h-full rounded-full bg-[var(--primary)] transition-all"
                                :style="{ width: `${enrollment.progress}%` }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
