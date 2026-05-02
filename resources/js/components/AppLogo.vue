<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
</script>

<template>
    <div
        class="flex aspect-square size-9 items-center justify-center rounded-lg"
        style="background-color: var(--primary);"
    >
        <img
            v-if="page.props.appDetails.logoUrl"
            :src="page.props.appDetails.logoUrl"
            :alt="page.props.appDetails.name"
            class="size-6 object-contain"
        />
        <AppLogoIcon v-else class="size-5 fill-current text-white" />
    </div>
    <div class="ml-1 grid flex-1 text-left">
        <span
            class="truncate leading-tight font-black text-base tracking-tight"
            style="color: var(--primary); font-family: Manrope, sans-serif;"
        >{{ page.props.appDetails.name }}</span>
    </div>
</template>
