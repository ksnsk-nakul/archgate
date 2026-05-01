<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, ChevronDown, ChevronRight, PlayCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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

    if (!val) {
return [];
}

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
</script>

<template>
    <Head :title="course.data.title" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader
                :title="course.data.title"
                description="Course sections, lessons, and enrollment progress."
            />
            <Button variant="outline" as-child>
                <Link :href="coursesIndex()">
                    <ArrowLeft class="size-4" />
                    Back to courses
                </Link>
            </Button>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <!-- Course meta + enrollment -->
            <div class="flex flex-col gap-4 lg:col-span-1">
                <Card class="rounded-lg">
                    <CardContent class="flex flex-col gap-4 pt-6">
                        <div class="flex flex-wrap gap-2">
                            <Badge variant="outline">{{ course.data.status ?? 'draft' }}</Badge>
                            <Badge variant="secondary">{{ totalLessons() }} lessons</Badge>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            {{ course.data.description || 'No description provided.' }}
                        </p>

                        <div v-if="enrollment">
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
                            <Badge class="mt-3" variant="secondary">{{ enrollment.status }}</Badge>
                        </div>

                        <Button v-else :disabled="enrollForm.processing" @click="enroll">
                            Enroll in course
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- Sections + lessons -->
            <div class="flex flex-col gap-3 lg:col-span-2">
                <p v-if="sections.data.length === 0" class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
                    No sections have been added yet.
                </p>

                <Card
                    v-for="section in sections.data"
                    :key="section.id"
                    class="rounded-lg"
                >
                    <CardHeader
                        class="cursor-pointer select-none"
                        @click="toggleSection(section.id)"
                    >
                        <CardTitle class="flex items-center justify-between text-base">
                            <div class="flex items-center gap-2">
                                <BookOpen class="size-4 text-muted-foreground" />
                                {{ section.title }}
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge variant="secondary">{{ lessonsFor(section).length }}</Badge>
                                <ChevronDown v-if="expandedSections.has(section.id)" class="size-4 text-muted-foreground" />
                                <ChevronRight v-else class="size-4 text-muted-foreground" />
                            </div>
                        </CardTitle>
                    </CardHeader>

                    <CardContent v-if="expandedSections.has(section.id)" class="flex flex-col gap-2 pt-0">
                        <Link
                            v-for="lesson in lessonsFor(section)"
                            :key="lesson.id"
                            :href="`/lessons/${lesson.id}`"
                            class="flex items-center gap-3 rounded-md border p-3 text-sm transition-colors hover:bg-muted/50"
                        >
                            <PlayCircle class="size-4 shrink-0 text-muted-foreground" />
                            <span class="flex-1">{{ lesson.title }}</span>
                            <Badge v-if="lesson.type" variant="outline" class="text-xs">{{ lesson.type }}</Badge>
                        </Link>
                        <p v-if="lessonsFor(section).length === 0" class="text-sm text-muted-foreground">
                            No lessons in this section yet.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
