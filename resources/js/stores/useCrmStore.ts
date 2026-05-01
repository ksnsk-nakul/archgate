import { defineStore } from 'pinia';
import { ref } from 'vue';
import { index as listContacts } from '@/routes/contacts';
import { index as listDeals } from '@/routes/deals';
import { apiRequest } from './api';
import type { ApiCollection } from './api';

export type Contact = {
    id: number;
    name: string;
    email?: string | null;
    phone?: string | null;
    status?: string | null;
};

export type Deal = {
    id: number;
    title: string;
    value?: number | null;
    status?: string | null;
    stage?: {
        id: number;
        name: string;
    } | null;
};

export const useCrmStore = defineStore('crm', () => {
    const contacts = ref<Contact[]>([]);
    const deals = ref<Deal[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    function hydrateContacts(initialContacts: Contact[]): void {
        contacts.value = initialContacts;
    }

    function hydrateDeals(initialDeals: Deal[]): void {
        deals.value = initialDeals;
    }

    async function fetchContacts(): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response =
                await apiRequest<ApiCollection<Contact>>(listContacts.url());
            contacts.value = response.data;
        } catch (caught) {
            error.value =
                caught instanceof Error
                    ? caught.message
                    : 'Unable to load contacts';
        } finally {
            loading.value = false;
        }
    }

    async function fetchDeals(): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiRequest<ApiCollection<Deal>>(listDeals.url());
            deals.value = response.data;
        } catch (caught) {
            error.value =
                caught instanceof Error ? caught.message : 'Unable to load deals';
        } finally {
            loading.value = false;
        }
    }

    return {
        contacts,
        deals,
        error,
        fetchContacts,
        fetchDeals,
        hydrateContacts,
        hydrateDeals,
        loading,
    };
});
