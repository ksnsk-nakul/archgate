<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

type LibraryItem = { id: number; title: string; type: string; cover_url: string | null };
defineProps<{ items: LibraryItem[] }>();
const page = usePage<{ appDetails: { name: string }; auth: { user: unknown } }>();
const appName = computed(() => page.props.appDetails?.name ?? 'FluxHaven');
const isAuthed = computed(() => !!page.props.auth?.user);

function readHref(item: LibraryItem): string {
    return isAuthed.value ? `/library/${item.id}/read` : `/login?intended=/library/${item.id}/read`;
}

const typeBadgeClass: Record<string, string> = {
    book:    'text-blue-400 bg-blue-400/10 border-blue-400/20',
    article: 'text-green-400 bg-green-400/10 border-green-400/20',
    video:   'text-purple-400 bg-purple-400/10 border-purple-400/20',
};
</script>

<template>
    <Head :title="`Library — ${appName}`" />

    <!-- Hero -->
    <section class="py-20 px-6 text-center" style="background: linear-gradient(135deg, #051424 0%, #0a1929 60%, #051424 100%);">
        <div class="max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 bg-[#f7600d]/10 border border-[#f7600d]/20 text-[#f7600d] text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-6">Library</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight" style="font-family: Manrope, sans-serif;">Explore our library</h1>
            <p class="text-lg text-slate-400">Books, articles, and resources to accelerate your learning.</p>
        </div>
    </section>

    <!-- Items grid -->
    <section class="py-16 px-6 bg-[#051424]">
        <div class="max-w-6xl mx-auto">
            <div v-if="items.length === 0" class="text-center py-20">
                <div class="size-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto mb-5">
                    <svg class="size-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2" style="font-family: Manrope, sans-serif;">Our library is being curated</h3>
                <p class="text-slate-500 text-sm">Check back soon for books, articles, and more.</p>
                <a v-if="!isAuthed" href="/register" class="inline-flex items-center gap-2 mt-6 bg-[#f7600d] hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">
                    Create an account to get notified
                </a>
            </div>

            <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <div
                    v-for="item in items"
                    :key="item.id"
                    class="group rounded-2xl border border-slate-800 bg-[#0d1c2d] overflow-hidden hover:border-[#f7600d]/30 transition-colors flex flex-col"
                >
                    <!-- Cover -->
                    <div class="aspect-[3/2] bg-slate-800 flex items-center justify-center overflow-hidden">
                        <img v-if="item.cover_url" :src="item.cover_url" :alt="item.title" class="w-full h-full object-cover" />
                        <svg v-else class="size-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <!-- Info -->
                    <div class="p-4 flex flex-col gap-3 flex-1">
                        <span class="text-xs font-semibold border px-2 py-0.5 rounded-full w-fit" :class="typeBadgeClass[item.type] ?? 'text-slate-400 bg-slate-800 border-slate-700'">{{ item.type }}</span>
                        <h3 class="text-sm font-bold text-white leading-snug" style="font-family: Manrope, sans-serif;">{{ item.title }}</h3>
                        <div class="mt-auto pt-2">
                            <a :href="readHref(item)" class="flex items-center justify-center gap-1.5 w-full text-xs font-semibold border border-[#f7600d]/30 text-[#f7600d] hover:bg-[#f7600d] hover:text-white px-3 py-2 rounded-lg transition-colors">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
                                {{ isAuthed ? 'Read' : 'Sign in to read' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
