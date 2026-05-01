import { defineStore } from 'pinia';
import { ref } from 'vue';
import {
    destroy as destroyProject,
    index as listProjects,
    store as storeProject,
} from '@/routes/projects';
import { apiRequest } from './api';
import type { ApiCollection, ApiItem } from './api';

export type Project = {
    id: number;
    name: string;
    description?: string | null;
    status: 'active' | 'completed' | 'archived';
};

type ProjectPayload = {
    name: string;
    description?: string;
    status?: Project['status'];
};

export const useProjectStore = defineStore('projects', () => {
    const projects = ref<Project[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    function hydrateProjects(initialProjects: Project[]): void {
        projects.value = initialProjects;
    }

    async function fetchProjects(): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response =
                await apiRequest<ApiCollection<Project>>(listProjects.url());
            projects.value = response.data;
        } catch (caught) {
            error.value =
                caught instanceof Error
                    ? caught.message
                    : 'Unable to load projects';
        } finally {
            loading.value = false;
        }
    }

    async function createProject(payload: ProjectPayload): Promise<void> {
        const optimisticProject: Project = {
            id: Date.now(),
            name: payload.name,
            description: payload.description,
            status: payload.status ?? 'active',
        };

        projects.value = [optimisticProject, ...projects.value];

        try {
            const response = await apiRequest<ApiItem<Project>>(
                storeProject.url(),
                {
                    method: 'POST',
                    body: JSON.stringify(payload),
                },
            );

            projects.value = projects.value.map((project) =>
                project.id === optimisticProject.id ? response.data : project,
            );
        } catch (caught) {
            projects.value = projects.value.filter(
                (project) => project.id !== optimisticProject.id,
            );
            error.value =
                caught instanceof Error
                    ? caught.message
                    : 'Unable to create project';
        }
    }

    async function deleteProject(id: number): Promise<void> {
        const snapshot = [...projects.value];
        projects.value = projects.value.filter((p) => p.id !== id);

        try {
            await apiRequest<void>(destroyProject.url(id), { method: 'DELETE' });
        } catch (caught) {
            projects.value = snapshot;
            error.value =
                caught instanceof Error
                    ? caught.message
                    : 'Unable to delete project';
        }
    }

    return {
        createProject,
        deleteProject,
        error,
        fetchProjects,
        hydrateProjects,
        loading,
        projects,
    };
});
