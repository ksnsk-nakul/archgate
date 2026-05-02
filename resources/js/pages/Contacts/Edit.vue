<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

type Contact = {
    id: number;
    name: string;
    email?: string | null;
    phone?: string | null;
};

const props = defineProps<{
    contact: { data: Contact };
}>();

const form = useForm({
    name: props.contact.data.name,
    email: props.contact.data.email ?? '',
    phone: props.contact.data.phone ?? '',
});

function submit(): void {
    form.put(`/contacts/${props.contact.data.id}`);
}
</script>

<template>
    <Head title="Edit contact" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-app">
            <Link
                :href="`/contacts/${contact.data.id}`"
                class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-app border border-app hover:border-slate-600 px-3 py-2 rounded-lg transition-colors"
            >
                <ArrowLeft class="size-3.5" /> Back
            </Link>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">CRM</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Edit: {{ contact.data.name }}</h1>
            </div>
        </div>

        <form class="px-6 py-6 max-w-2xl flex flex-col gap-5" @submit.prevent="submit">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Contact details</h2>
                </div>
                <div class="px-6 py-5 flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Name <span class="text-red-400">*</span></label>
                        <input
                            id="name"
                            v-model="form.name"
                            placeholder="Jane Doe"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-400">{{ form.errors.name }}</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="email" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="jane@example.com"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-400">{{ form.errors.email }}</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="phone" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Phone</label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            placeholder="+1 555 000 0000"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': form.errors.phone }"
                        />
                        <p v-if="form.errors.phone" class="text-xs text-red-400">{{ form.errors.phone }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                >
                    <svg v-if="form.processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    {{ form.processing ? 'Saving…' : 'Save changes' }}
                </button>
                <Link
                    :href="`/contacts/${contact.data.id}`"
                    class="text-xs font-semibold text-slate-400 border border-app hover:text-app hover:border-slate-600 px-4 py-2.5 rounded-lg transition-colors"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </div>
</template>
