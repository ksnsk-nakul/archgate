<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

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
    <Head title="New deal" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-app">
            <Link
                href="/deals"
                class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-app border border-app hover:border-slate-600 px-3 py-2 rounded-lg transition-colors"
            >
                <ArrowLeft class="size-3.5" /> Deals
            </Link>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">CRM</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">New deal</h1>
            </div>
        </div>

        <form class="px-6 py-6 max-w-2xl flex flex-col gap-5" @submit.prevent="submit">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Deal details</h2>
                </div>
                <div class="px-6 py-5 flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="title" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Title <span class="text-red-400">*</span></label>
                        <input
                            id="title"
                            v-model="form.title"
                            placeholder="Enterprise contract"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="text-xs text-red-400">{{ form.errors.title }}</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="value" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Value ($)</label>
                        <input
                            id="value"
                            v-model="form.value"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="5000"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': form.errors.value }"
                        />
                        <p v-if="form.errors.value" class="text-xs text-red-400">{{ form.errors.value }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="stage_id" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stage <span class="text-red-400">*</span></label>
                            <select
                                id="stage_id"
                                v-model="form.stage_id"
                                required
                                class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                                :class="{ 'border-red-500': form.errors.stage_id }"
                            >
                                <option v-for="stage in stages.data" :key="stage.id" :value="stage.id">{{ stage.name }}</option>
                            </select>
                            <p v-if="form.errors.stage_id" class="text-xs text-red-400">{{ form.errors.stage_id }}</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="contact_id" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Contact</label>
                            <select
                                id="contact_id"
                                v-model="form.contact_id"
                                class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            >
                                <option value="">No contact</option>
                                <option v-for="contact in contacts.data" :key="contact.id" :value="contact.id">{{ contact.name }}</option>
                            </select>
                        </div>
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
                    {{ form.processing ? 'Creating…' : 'Create deal' }}
                </button>
                <Link
                    href="/deals"
                    class="text-xs font-semibold text-slate-400 border border-app hover:text-app hover:border-slate-600 px-4 py-2.5 rounded-lg transition-colors"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </div>
</template>
