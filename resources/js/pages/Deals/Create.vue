<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Stage = { id: number; name: string };
type Contact = { id: number; name: string };

const props = defineProps<{
    stages: { data: Stage[] };
    contacts: { data: Contact[] };
}>();

const form = useForm({
    title: '',
    value: '',
    stage_id: props.stages.data[0]?.id ?? '',
    contact_id: '',
});

function submit(): void {
    form.post('/deals');
}
</script>

<template>
    <Head title="New Deal" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader title="New deal" description="Add a deal to your CRM pipeline." />
            <Button variant="outline" as-child>
                <Link href="/deals">
                    <ArrowLeft class="size-4" />
                    Back to deals
                </Link>
            </Button>
        </div>

        <Card class="max-w-lg rounded-lg">
            <CardHeader>
                <CardTitle>Deal details</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <div class="flex flex-col gap-1.5">
                        <Label for="title">Title <span class="text-destructive">*</span></Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            placeholder="Enterprise contract"
                            :class="{ 'border-destructive': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="text-xs text-destructive">{{ form.errors.title }}</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="value">Value ($)</Label>
                        <Input
                            id="value"
                            v-model="form.value"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="5000"
                            :class="{ 'border-destructive': form.errors.value }"
                        />
                        <p v-if="form.errors.value" class="text-xs text-destructive">{{ form.errors.value }}</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="stage_id">Stage <span class="text-destructive">*</span></Label>
                        <select
                            id="stage_id"
                            v-model="form.stage_id"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            :class="{ 'border-destructive': form.errors.stage_id }"
                        >
                            <option v-for="stage in stages.data" :key="stage.id" :value="stage.id">
                                {{ stage.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.stage_id" class="text-xs text-destructive">{{ form.errors.stage_id }}</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="contact_id">Contact</Label>
                        <select
                            id="contact_id"
                            v-model="form.contact_id"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option value="">No contact</option>
                            <option v-for="contact in contacts.data" :key="contact.id" :value="contact.id">
                                {{ contact.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <Button variant="outline" as-child>
                            <Link href="/deals">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Create deal' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
