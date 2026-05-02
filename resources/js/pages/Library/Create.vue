<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookMarked } from 'lucide-vue-next';

const form = useForm({
    title: '',
    type: 'article',
    description: '',
    access_level: 'free',
});

function submit(): void {
    form.post('/library');
}
</script>

<template>
    <Head title="Add Library Item" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div class="flex items-center gap-3">
                <Link
                    href="/library"
                    class="flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-app border border-app hover:border-slate-600 px-3 py-2 rounded-lg transition-colors"
                >
                    <ArrowLeft class="size-3.5" />
                    Back
                </Link>
                <div>
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-widest mb-0.5">Knowledge</p>
                    <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Add Library Item</h1>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 max-w-2xl">
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <!-- Card header -->
                <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                    <div class="size-9 rounded-lg bg-blue-500/10 flex items-center justify-center">
                        <BookMarked class="size-4 text-blue-400" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Item details</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Add a new piece of content to the library.</p>
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
                            placeholder="e.g. Getting Started with Laravel"
                            required
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="text-xs text-red-400">{{ form.errors.title }}</p>
                    </div>

                    <!-- Type + Access level (side by side) -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="type" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Type <span class="text-red-400">*</span>
                            </label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                                :class="{ 'border-red-500': form.errors.type }"
                            >
                                <option value="article">Article</option>
                                <option value="video">Video</option>
                                <option value="pdf">PDF</option>
                                <option value="audio">Audio</option>
                            </select>
                            <p v-if="form.errors.type" class="text-xs text-red-400">{{ form.errors.type }}</p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="access_level" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Access level</label>
                            <select
                                id="access_level"
                                v-model="form.access_level"
                                class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors"
                            >
                                <option value="free">Free — everyone</option>
                                <option value="basic">Basic — subscribers</option>
                                <option value="premium">Premium — premium only</option>
                            </select>
                            <p v-if="form.errors.access_level" class="text-xs text-red-400">{{ form.errors.access_level }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col gap-2">
                        <label for="description" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            placeholder="A short summary of this content..."
                            class="input-app rounded-lg px-4 py-2.5 text-sm border transition-colors resize-none"
                        />
                        <p v-if="form.errors.description" class="text-xs text-red-400">{{ form.errors.description }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/library"
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
                            {{ form.processing ? 'Saving...' : 'Add to library' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
