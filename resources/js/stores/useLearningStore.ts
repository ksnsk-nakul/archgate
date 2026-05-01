import { defineStore } from 'pinia';
import { ref } from 'vue';
import { index as listContent } from '@/actions/App/Http/Controllers/LibraryContentController';
import { index as listCourses } from '@/routes/courses';
import { index as listEnrollments } from '@/routes/enrollments';
import { index as listSubscriptions } from '@/routes/subscriptions';
import { apiRequest } from './api';
import type { ApiCollection } from './api';

export type LibraryContent = {
    id: number;
    title: string;
    type?: string | null;
    access_level?: string | null;
};

export type Course = {
    id: number;
    title: string;
    description?: string | null;
};

export type Enrollment = {
    id: number;
    course_id: number;
    progress: number;
    status: string;
};

export type SubscriptionPlan = {
    id: number;
    name: string;
    price?: number | null;
};

export const useLearningStore = defineStore('learning', () => {
    const content = ref<LibraryContent[]>([]);
    const courses = ref<Course[]>([]);
    const enrollments = ref<Enrollment[]>([]);
    const subscriptions = ref<SubscriptionPlan[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    function hydrateLibrary(
        initialContent: LibraryContent[],
        initialSubscriptions: SubscriptionPlan[],
    ): void {
        content.value = initialContent;
        subscriptions.value = initialSubscriptions;
    }

    function hydrateCourses(
        initialCourses: Course[],
        initialEnrollments: Enrollment[],
    ): void {
        courses.value = initialCourses;
        enrollments.value = initialEnrollments;
    }

    async function loadLibrary(): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const [contentResponse, subscriptionResponse] = await Promise.all([
                apiRequest<ApiCollection<LibraryContent>>(listContent.url()),
                apiRequest<ApiCollection<SubscriptionPlan>>(listSubscriptions.url()),
            ]);

            content.value = contentResponse.data;
            subscriptions.value = subscriptionResponse.data;
        } catch (caught) {
            error.value =
                caught instanceof Error ? caught.message : 'Unable to load library';
        } finally {
            loading.value = false;
        }
    }

    async function loadCourses(): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const [courseResponse, enrollmentResponse] = await Promise.all([
                apiRequest<ApiCollection<Course>>(listCourses.url()),
                apiRequest<ApiCollection<Enrollment>>(listEnrollments.url()),
            ]);

            courses.value = courseResponse.data;
            enrollments.value = enrollmentResponse.data;
        } catch (caught) {
            error.value =
                caught instanceof Error ? caught.message : 'Unable to load courses';
        } finally {
            loading.value = false;
        }
    }

    return {
        content,
        courses,
        enrollments,
        error,
        hydrateCourses,
        hydrateLibrary,
        loadCourses,
        loadLibrary,
        loading,
        subscriptions,
    };
});
