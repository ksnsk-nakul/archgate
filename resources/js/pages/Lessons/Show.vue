<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, BookOpen, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

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

    if (!val) {
return [];
}

    return Array.isArray(val) ? val : (val as { data: SectionLesson[] }).data;
}

// Flatten all lessons across all sections in order
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
</script>

<template>
    <Head :title="lesson.data.title" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Breadcrumb nav -->
        <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <Link :href="`/courses/${course.data.id}`" class="hover:text-foreground">
                {{ course.data.title }}
            </Link>
            <ChevronRight class="size-3" />
            <span v-if="currentSection">{{ currentSection.title }}</span>
            <ChevronRight class="size-3" />
            <span class="text-foreground">{{ lesson.data.title }}</span>
        </div>

        <div class="grid gap-4 lg:grid-cols-4">
            <!-- Lesson content -->
            <div class="flex flex-col gap-4 lg:col-span-3">
                <div class="rounded-lg border bg-card p-6">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <h1 class="text-xl font-semibold">{{ lesson.data.title }}</h1>
                        <Badge v-if="lesson.data.type" variant="outline">{{ lesson.data.type }}</Badge>
                    </div>
                    <article class="prose prose-sm dark:prose-invert max-w-none">
                        <p v-if="lesson.data.content" class="whitespace-pre-wrap text-sm leading-relaxed text-muted-foreground">
                            {{ lesson.data.content }}
                        </p>
                        <p v-else class="text-sm text-muted-foreground">
                            No content has been added to this lesson yet.
                        </p>
                    </article>
                </div>

                <!-- Prev / Next navigation -->
                <div class="flex items-center justify-between gap-4">
                    <Button v-if="prevLesson" variant="outline" as-child>
                        <Link :href="`/lessons/${prevLesson.id}`">
                            <ArrowLeft class="size-4" />
                            {{ prevLesson.title }}
                        </Link>
                    </Button>
                    <div v-else />
                    <Button v-if="nextLesson" as-child>
                        <Link :href="`/lessons/${nextLesson.id}`">
                            {{ nextLesson.title }}
                            <ArrowRight class="size-4" />
                        </Link>
                    </Button>
                    <Button v-else variant="outline" as-child>
                        <Link :href="`/courses/${course.data.id}`">
                            Back to course
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Sidebar: course outline -->
            <div class="lg:col-span-1">
                <Card class="rounded-lg">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-sm">
                            <BookOpen class="size-4" />
                            Course outline
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-3 pt-0">
                        <div v-for="section in sections.data" :key="section.id">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                {{ section.title }}
                            </p>
                            <div class="flex flex-col gap-1">
                                <Link
                                    v-for="l in lessonsFor(section)"
                                    :key="l.id"
                                    :href="`/lessons/${l.id}`"
                                    class="rounded-md px-2 py-1.5 text-sm transition-colors hover:bg-muted/50"
                                    :class="l.id === lesson.data.id ? 'bg-muted font-medium' : 'text-muted-foreground'"
                                >
                                    {{ l.title }}
                                </Link>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Progress card if enrolled -->
                <Card v-if="enrollment" class="mt-3 rounded-lg">
                    <CardContent class="pt-4">
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-muted-foreground">Progress</span>
                            <span class="font-medium">{{ enrollment.progress }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-muted">
                            <div
                                class="h-full rounded-full bg-primary transition-all"
                                :style="{ width: `${enrollment.progress}%` }"
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
