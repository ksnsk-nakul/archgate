<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{ modelValue: string; placeholder?: string; minHeight?: string }>();
const emit  = defineEmits<{ (e: 'update:modelValue', val: string): void }>();

const editor = ref<HTMLDivElement | null>(null);

onMounted(() => {
    if (editor.value) {
        editor.value.innerHTML = props.modelValue ?? '';
    }
});

watch(() => props.modelValue, (val) => {
    if (editor.value && editor.value.innerHTML !== val) {
        editor.value.innerHTML = val ?? '';
    }
});

function onInput() {
    emit('update:modelValue', editor.value?.innerHTML ?? '');
}

function exec(cmd: string, value?: string) {
    document.execCommand(cmd, false, value);
    editor.value?.focus();
    onInput();
}

function isActive(cmd: string): boolean {
    try { return document.queryCommandState(cmd); } catch { return false; }
}

const toolbarButtons = [
    { cmd: 'bold',          icon: 'B',  label: 'Bold',          style: 'font-bold' },
    { cmd: 'italic',        icon: 'I',  label: 'Italic',        style: 'italic' },
    { cmd: 'underline',     icon: 'U',  label: 'Underline',     style: 'underline' },
    { cmd: 'insertUnorderedList', icon: '•', label: 'Bullet list', style: '' },
    { cmd: 'insertOrderedList',   icon: '1.', label: 'Numbered list', style: '' },
];
</script>

<template>
    <div class="rounded-lg border border-slate-700 bg-slate-900 overflow-hidden focus-within:border-[var(--primary)] focus-within:ring-1 focus-within:ring-[var(--primary)]/20 transition-colors">
        <!-- Toolbar -->
        <div class="flex items-center gap-0.5 px-2 py-1.5 border-b border-slate-700 bg-slate-800/50">
            <button
                v-for="btn in toolbarButtons"
                :key="btn.cmd"
                type="button"
                :title="btn.label"
                @click="exec(btn.cmd)"
                class="w-7 h-7 rounded text-xs flex items-center justify-center transition-colors"
                :class="[btn.style, isActive(btn.cmd) ? 'bg-[var(--primary)]/20 text-[var(--primary)]' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-700']"
            >
                {{ btn.icon }}
            </button>
            <div class="mx-1 w-px h-4 bg-slate-700" />
            <button type="button" title="Heading 2" @click="exec('formatBlock', 'h2')" class="px-2 h-7 rounded text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-700 transition-colors">H2</button>
            <button type="button" title="Heading 3" @click="exec('formatBlock', 'h3')" class="px-2 h-7 rounded text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-700 transition-colors">H3</button>
            <button type="button" title="Paragraph" @click="exec('formatBlock', 'p')"  class="px-2 h-7 rounded text-xs text-slate-400 hover:text-slate-200 hover:bg-slate-700 transition-colors">¶</button>
            <div class="mx-1 w-px h-4 bg-slate-700" />
            <button type="button" title="Remove formatting" @click="exec('removeFormat')" class="px-2 h-7 rounded text-xs text-slate-400 hover:text-red-400 hover:bg-slate-700 transition-colors">✕</button>
        </div>
        <!-- Editable area -->
        <div
            ref="editor"
            contenteditable="true"
            class="px-4 py-3 text-sm text-slate-100 outline-none leading-relaxed"
            :style="{ minHeight: minHeight ?? '120px' }"
            :data-placeholder="placeholder ?? 'Start typing...'"
            @input="onInput"
        />
    </div>
</template>

<style scoped>
[contenteditable]:empty:before {
    content: attr(data-placeholder);
    color: rgb(100 116 139);
    pointer-events: none;
}
[contenteditable] :deep(h2) { font-size: 1.1em; font-weight: 700; margin: 0.5em 0 0.25em; }
[contenteditable] :deep(h3) { font-size: 1em; font-weight: 600; margin: 0.5em 0 0.25em; }
[contenteditable] :deep(ul) { list-style: disc; padding-left: 1.25em; margin: 0.25em 0; }
[contenteditable] :deep(ol) { list-style: decimal; padding-left: 1.25em; margin: 0.25em 0; }
[contenteditable] :deep(p)  { margin: 0.15em 0; }
</style>
