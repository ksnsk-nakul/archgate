<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useUserStore } from '@/stores/useUserStore';

const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const userStore = useUserStore();

const props = defineProps<{
    profile: {
        data: {
            id: number;
            name: string;
            email: string;
        };
    };
}>();

userStore.hydrateUser(props.profile.data);

const form = reactive({
    name: currentUser.value.name,
    email: currentUser.value.email,
    password: '',
});

async function submit(): Promise<void> {
    await userStore.updateProfile({
        email: form.email,
        name: form.name,
    });
}
</script>

<template>
    <Head title="Edit profile" />

    <form class="flex max-w-2xl flex-col gap-6 p-4" @submit.prevent="submit">
        <PageHeader title="Edit profile" description="Update account details through the user API." />

        <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" v-model="form.name" name="name" required />
        </div>

        <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input id="email" v-model="form.email" name="email" type="email" required />
        </div>

        <div class="grid gap-2">
            <Label for="password">New password</Label>
            <Input id="password" v-model="form.password" name="password" type="password" autocomplete="new-password" />
        </div>

        <div v-if="userStore.error" class="rounded-md border border-destructive/40 p-3 text-sm text-destructive">
            {{ userStore.error }}
        </div>

        <Button type="submit">Save profile</Button>
    </form>
</template>
