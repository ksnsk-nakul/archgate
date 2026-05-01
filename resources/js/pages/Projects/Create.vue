<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/app/projects';
</script>

<template>
    <Head title="Create project" />

    <Form
        v-bind="store.form()"
        class="flex max-w-2xl flex-col gap-6 p-4"
        v-slot="{ errors, processing }"
    >
        <PageHeader title="Create project" description="Add the minimum project details required to start tracking work." />

        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" name="name" required />
            <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="description">Description</Label>
            <Input id="description" name="description" />
            <InputError :message="errors.description" />
        </div>

        <input name="status" type="hidden" value="active" />

        <div class="flex gap-3">
            <Button type="submit" :disabled="processing">Create project</Button>
            <Button variant="outline" type="button" @click="router.visit('/projects')">
                Cancel
            </Button>
        </div>
    </Form>
</template>
