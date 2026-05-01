<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, ChevronRight } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useLearningStore } from '@/stores/useLearningStore';
import type { Course, Enrollment } from '@/stores/useLearningStore';

const learningStore = useLearningStore();

const props = defineProps<{
    courses: {
        data: Course[];
    };
    enrollments: {
        data: Enrollment[];
    };
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

    <div class="flex flex-col gap-6 p-4">
        <PageHeader title="Courses" description="Manage enrollments and continue lessons." />

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Card v-for="course in learningStore.courses" :key="course.id" class="flex flex-col rounded-lg">
                <CardHeader>
                    <div class="flex items-start justify-between gap-2">
                        <CardTitle class="text-base leading-snug">
                            <Link :href="`/courses/${course.id}`" class="hover:underline">{{ course.title }}</Link>
                        </CardTitle>
                        <Badge v-if="isEnrolled(course.id)" variant="secondary" class="shrink-0 text-xs">Enrolled</Badge>
                    </div>
                </CardHeader>
                <CardContent class="flex flex-1 flex-col gap-3">
                    <p class="flex-1 text-sm text-muted-foreground">
                        {{ course.description || 'No course description yet.' }}
                    </p>

                    <div v-if="isEnrolled(course.id)" class="space-y-1">
                        <div class="flex justify-between text-xs text-muted-foreground">
                            <span>Progress</span>
                            <span>{{ progressFor(course.id) }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-muted">
                            <div
                                class="h-full rounded-full bg-primary transition-all"
                                :style="{ width: `${progressFor(course.id)}%` }"
                            />
                        </div>
                    </div>

                    <Button variant="outline" size="sm" as-child>
                        <Link :href="`/courses/${course.id}`">
                            {{ isEnrolled(course.id) ? 'Continue course' : 'View course' }}
                            <ChevronRight class="size-3.5" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>

        <div v-if="!learningStore.loading && learningStore.courses.length === 0" class="rounded-lg border border-dashed p-12 text-center">
            <BookOpen class="mx-auto mb-3 size-8 text-muted-foreground" />
            <p class="mb-1 font-medium">No courses available</p>
            <p class="text-sm text-muted-foreground">Check back later — new courses will appear here when published.</p>
        </div>
    </div>
</template>
