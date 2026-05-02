<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { update } from '@/routes/app/projects';
import type { Project } from '@/stores/useProjectStore';

defineProps<{
    project: { data: Project };
}>();
</script>

<template>
    <Head title="Edit project" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-app">
            <button
                type="button"
                class="flex items-center gap-1.5 text-xs font-semibold text-app-muted hover:text-app border border-app hover:border-app px-3 py-2 rounded-lg transition-colors"
                @click="router.visit(`/projects/${project.data.id}`)"
            >
                <ArrowLeft class="size-3.5" /> Back
            </button>
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Projects</p>
                <h1 class="text-xl font-bold text-app truncate max-w-sm" style="font-family: Manrope, sans-serif;">Edit: {{ project.data.name }}</h1>
            </div>
        </div>

        <Form v-bind="update.form(project.data)" class="px-6 py-6 max-w-2xl flex flex-col gap-5" v-slot="{ errors, processing }">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app">
                    <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Project details</h2>
                </div>
                <div class="px-6 py-5 flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Name <span class="text-red-400">*</span></label>
                        <input
                            id="name"
                            name="name"
                            :default-value="project.data.name"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': errors.name }"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="description" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Description</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            :default-value="project.data.description ?? ''"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors resize-none"
                        />
                        <InputError :message="errors.description" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="status" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                        >
                            <option value="active" :selected="project.data.status === 'active'">Active</option>
                            <option value="completed" :selected="project.data.status === 'completed'">Completed</option>
                            <option value="archived" :selected="project.data.status === 'archived'">Archived</option>
                        </select>
                        <InputError :message="errors.status" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="processing"
                    class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                >
                    <svg v-if="processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    {{ processing ? 'Saving…' : 'Save project' }}
                </button>
                <button
                    type="button"
                    class="text-xs font-semibold text-app-muted border border-app hover:text-app hover:border-app px-4 py-2.5 rounded-lg transition-colors"
                    @click="router.visit(`/projects/${project.data.id}`)"
                >
                    Cancel
                </button>
            </div>
        </Form>
    </div>
</template>
