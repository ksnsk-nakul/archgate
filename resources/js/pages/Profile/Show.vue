<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
</script>

<template>
    <Head title="Profile" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <PageHeader title="Profile" description="Current account identity and access context." />
            <Button as-child>
                <Link href="/profile/edit">Edit profile</Link>
            </Button>
        </div>

        <Card class="max-w-2xl rounded-lg">
            <CardHeader>
                <CardTitle>{{ userStore.user?.name ?? currentUser.name }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-3 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Email</span>
                    <span>{{ userStore.user?.email ?? currentUser.email }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-muted-foreground">Role</span>
                    <span>{{ userStore.role }}</span>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
