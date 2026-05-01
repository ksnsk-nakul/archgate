<script setup lang="ts">
defineProps<{
    title: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold tracking-normal text-foreground">
            {{ title }}
        </h1>
        <p v-if="description" class="max-w-3xl text-sm text-muted-foreground">
            {{ description }}
        </p>
    </div>
</template>
