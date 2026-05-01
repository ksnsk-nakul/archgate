import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { show as showUser, update as updateUser } from '@/routes/users';
import { apiRequest } from './api';
import type { ApiItem } from './api';

type User = {
    id: number;
    name: string;
    email: string;
    role?: string;
};

export const useUserStore = defineStore('user', () => {
    const user = ref<User | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const role = computed(() => user.value?.role ?? 'Member');

    function hydrateUser(initialUser: User): void {
        user.value = initialUser;
    }

    async function fetchUser(id: number): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiRequest<ApiItem<User>>(showUser.url(id));
            user.value = response.data;
        } catch (caught) {
            error.value =
                caught instanceof Error ? caught.message : 'Unable to load user';
        } finally {
            loading.value = false;
        }
    }

    async function updateProfile(payload: Partial<User>): Promise<void> {
        if (!user.value) {
            return;
        }

        const previous = { ...user.value };
        user.value = { ...user.value, ...payload };

        try {
            const response = await apiRequest<ApiItem<User>>(
                updateUser.url(previous.id),
                {
                    method: 'PUT',
                    body: JSON.stringify(payload),
                },
            );
            user.value = response.data;
        } catch (caught) {
            user.value = previous;
            error.value =
                caught instanceof Error ? caught.message : 'Unable to update user';
        }
    }

    return { error, fetchUser, hydrateUser, loading, role, updateProfile, user };
});
