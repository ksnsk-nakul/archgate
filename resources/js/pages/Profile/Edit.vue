<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { computed, reactive } from 'vue';
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

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-app">
            <Link
                href="/profile"
                class="flex items-center gap-1.5 text-xs font-semibold text-app-muted hover:text-app border border-app hover:border-app px-3 py-2 rounded-lg transition-colors"
            >
                <ArrowLeft class="size-3.5" /> Profile
            </Link>
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Account</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Edit profile</h1>
            </div>
        </div>

        <form class="px-6 py-6 max-w-2xl flex flex-col gap-5" @submit.prevent="submit">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Account details</h2>
                </div>
                <div class="px-6 py-5 flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Name <span class="text-red-400">*</span></label>
                        <input
                            id="name"
                            v-model="form.name"
                            name="name"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                        />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="email" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Email <span class="text-red-400">*</span></label>
                        <input
                            id="email"
                            v-model="form.email"
                            name="email"
                            type="email"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                        />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password" class="text-xs font-semibold text-app-muted uppercase tracking-wider">New password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            placeholder="Leave blank to keep current"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                        />
                    </div>
                </div>
            </div>

            <div v-if="userStore.error" class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                {{ userStore.error }}
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                >
                    Save profile
                </button>
                <Link
                    href="/profile"
                    class="text-xs font-semibold text-app-muted border border-app hover:text-app hover:border-app px-4 py-2.5 rounded-lg transition-colors"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </div>
</template>
