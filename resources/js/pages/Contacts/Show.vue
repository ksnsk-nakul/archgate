<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Building2, Mail, Pencil, Phone, Trash2 } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Contact = {
    id: number;
    name: string;
    email?: string | null;
    phone?: string | null;
    organization_id?: number | null;
};

type Deal = {
    id: number;
    title: string;
    value?: number | null;
    stage?: { id: number; name: string } | null;
};

const props = defineProps<{
    contact: { data: Contact };
    deals: { data: Deal[] };
}>();

function deleteContact(): void {
    if (!confirm('Delete this contact? This cannot be undone.')) {
        return;
    }

    router.delete(`/contacts/${props.contact.data.id}`, {
        onSuccess: () => router.visit('/contacts'),
    });
}
</script>

<template>
    <Head :title="contact.data.name" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader
                :title="contact.data.name"
                description="Contact details, communication info, and linked deals."
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link :href="`/contacts/${contact.data.id}/edit`">
                        <Pencil class="size-4" />
                        Edit
                    </Link>
                </Button>
                <Button variant="outline" class="text-destructive hover:bg-destructive/10" @click="deleteContact">
                    <Trash2 class="size-4" />
                    Delete
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/contacts">
                        <ArrowLeft class="size-4" />
                        Back
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <Card class="rounded-lg lg:col-span-1">
                <CardHeader>
                    <CardTitle>Contact info</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-4 text-sm">
                    <div class="flex items-center gap-3">
                        <Mail class="size-4 shrink-0 text-muted-foreground" />
                        <a v-if="contact.data.email" :href="`mailto:${contact.data.email}`" class="underline underline-offset-2">
                            {{ contact.data.email }}
                        </a>
                        <span v-else class="text-muted-foreground">No email</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Phone class="size-4 shrink-0 text-muted-foreground" />
                        <span v-if="contact.data.phone">{{ contact.data.phone }}</span>
                        <span v-else class="text-muted-foreground">No phone</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Building2 class="size-4 shrink-0 text-muted-foreground" />
                        <span v-if="contact.data.organization_id">Org #{{ contact.data.organization_id }}</span>
                        <span v-else class="text-muted-foreground">No organization</span>
                    </div>
                </CardContent>
            </Card>

            <Card class="rounded-lg lg:col-span-2">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        Linked deals
                        <Badge variant="secondary">{{ deals.data.length }}</Badge>
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-3">
                    <div
                        v-for="deal in deals.data"
                        :key="deal.id"
                        class="flex items-center justify-between rounded-md border p-3"
                    >
                        <div>
                            <p class="font-medium">{{ deal.title }}</p>
                            <p class="text-sm text-muted-foreground">{{ deal.stage?.name ?? 'No stage' }}</p>
                        </div>
                        <p v-if="deal.value != null" class="font-semibold">
                            ${{ Number(deal.value).toLocaleString() }}
                        </p>
                        <p v-else class="text-sm text-muted-foreground">No value</p>
                    </div>
                    <p v-if="deals.data.length === 0" class="text-sm text-muted-foreground">
                        No deals linked to this contact.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
