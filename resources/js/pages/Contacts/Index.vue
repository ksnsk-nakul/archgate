<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Mail, Phone, Plus, UserRound } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useCrmStore } from '@/stores/useCrmStore';
import type { Contact } from '@/stores/useCrmStore';

const crmStore = useCrmStore();

const props = defineProps<{
    contacts: {
        data: Contact[];
    };
}>();

crmStore.hydrateContacts(props.contacts.data);

function deleteContact(id: number): void {
    if (!confirm('Delete this contact? This cannot be undone.')) {
        return;
    }

    router.delete(`/contacts/${id}`, {
        onSuccess: () => {
            crmStore.hydrateContacts(crmStore.contacts.filter((c) => c.id !== id));
        },
    });
}
</script>

<template>
    <Head title="Contacts" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader title="Contacts" description="Browse CRM contacts and lead status." />
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <Card v-for="contact in crmStore.contacts" :key="contact.id" class="rounded-lg">
                <CardHeader>
                    <CardTitle class="flex items-center justify-between gap-2 text-base">
                        <Link :href="`/contacts/${contact.id}`" class="hover:underline">{{ contact.name }}</Link>
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1 text-sm text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <Mail class="size-3.5 shrink-0" />
                            <span>{{ contact.email || 'No email' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Phone class="size-3.5 shrink-0" />
                            <span>{{ contact.phone || 'No phone' }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" as-child class="flex-1">
                            <Link :href="`/contacts/${contact.id}`">
                                <UserRound class="size-3.5" />
                                View
                            </Link>
                        </Button>
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="`/contacts/${contact.id}/edit`">Edit</Link>
                        </Button>
                        <Button variant="outline" size="sm" class="text-destructive hover:bg-destructive/10" @click="deleteContact(contact.id)">
                            Delete
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="!crmStore.loading && crmStore.contacts.length === 0" class="rounded-lg border border-dashed p-12 text-center">
            <UserRound class="mx-auto mb-3 size-8 text-muted-foreground" />
            <p class="mb-1 font-medium">No contacts yet</p>
            <p class="mb-4 text-sm text-muted-foreground">Start building your CRM by adding your first contact.</p>
        </div>
    </div>
</template>
