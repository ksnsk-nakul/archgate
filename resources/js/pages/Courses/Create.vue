<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen } from 'lucide-vue-next';

const form = useForm({
    title: '',
    description: '',
    status: 'draft',
});

function submit(): void {
    form.post('/courses');
}
</script>

<template>
    <Head title="New Course" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div class="flex items-center gap-3">
                <Link
                    href="/courses"
                    class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-app border border-app hover:border-slate-600 px-3 py-2 rounded-lg transition-colors"
                >
                    <ArrowLeft class="size-3.5" />
                    Back
                </Link>
                <div>
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">Learning</p>
                    <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">New Course</h1>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 max-w-2xl">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <!-- Card header -->
                <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                    <div class="size-9 rounded-lg bg-purple-500/10 flex items-center justify-center">
                        <BookOpen class="size-4 text-purple-400" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Course details</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Fill in the basic details to create the course.</p>
                    </div>
                </div>

                <!-- Form -->
                <form class="px-6 py-6 flex flex-col gap-5" @submit.prevent="submit">
                    <!-- Title -->
                    <div class="flex flex-col gap-2">
                        <label for="title" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Title <span class="text-red-400">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="e.g. Introduction to Web Development"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="text-xs text-red-400">{{ form.errors.title }}</p>
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col gap-2">
                        <label for="description" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            placeholder="What will students learn in this course?"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors resize-none"
                        />
                        <p v-if="form.errors.description" class="text-xs text-red-400">{{ form.errors.description }}</p>
                    </div>

                    <!-- Status -->
                    <div class="flex flex-col gap-2">
                        <label for="status" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                        >
                            <option value="draft">Draft — not visible to learners</option>
                            <option value="published">Published — visible to all</option>
                            <option value="archived">Archived</option>
                        </select>
                        <p v-if="form.errors.status" class="text-xs text-red-400">{{ form.errors.status }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/courses"
                            class="text-xs font-semibold text-slate-400 border border-app hover:text-app hover:border-slate-600 px-4 py-2.5 rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                        >
                            <svg v-if="form.processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            {{ form.processing ? 'Creating...' : 'Create course' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
