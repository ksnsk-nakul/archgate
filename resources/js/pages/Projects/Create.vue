<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { store } from '@/routes/app/projects';
</script>

<template>
    <Head title="Create project" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-app">
            <button
                type="button"
                class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-app border border-app hover:border-slate-600 px-3 py-2 rounded-lg transition-colors"
                @click="router.visit('/projects')"
            >
                <ArrowLeft class="size-3.5" /> Back
            </button>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">Projects</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Create project</h1>
            </div>
        </div>

        <Form v-bind="store.form()" class="px-6 py-6 max-w-2xl flex flex-col gap-5" v-slot="{ errors, processing }">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Project details</h2>
                </div>
                <div class="px-6 py-5 flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Name <span class="text-red-400">*</span></label>
                        <input
                            id="name"
                            name="name"
                            required
                            placeholder="e.g. Product Launch Q3"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': errors.name }"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="description" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Description</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Optional project overview…"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors resize-none"
                        />
                        <InputError :message="errors.description" />
                    </div>
                </div>
            </div>

            <input name="status" type="hidden" value="active" />

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="processing"
                    class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                >
                    <svg v-if="processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    {{ processing ? 'Creating…' : 'Create project' }}
                </button>
                <button
                    type="button"
                    class="text-xs font-semibold text-slate-400 border border-app hover:text-app hover:border-slate-600 px-4 py-2.5 rounded-lg transition-colors"
                    @click="router.visit('/projects')"
                >
                    Cancel
                </button>
            </div>
        </Form>
    </div>
</template>
