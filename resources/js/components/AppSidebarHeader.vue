<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="flex h-14 shrink-0 items-center gap-2 border-b border-app bg-app-surface px-4 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1 text-app-muted hover:text-app hover:bg-app-elevated rounded-md transition-colors" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <div class="h-4 w-px bg-app-border mx-1" />
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
    </header>
</template>
