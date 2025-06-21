<script setup>
const { document, copiedText, isFullTextOpen } = defineProps({
    document: {
        type: Object,
        default: null,
    },
    copiedText: String,
    isFullTextOpen: Boolean
})

const emit = defineEmits(['copy-text'])
</script>

<template>
    <details
        class="collapse collapse-arrow bg-base-100 border-base-300 border rounded-2xl" :open="isFullTextOpen">
        <summary class="collapse-title font-semibold">View Full Extracted Text</summary>
        <div class="collapse-content relative">
            <div class="absolute right-12 -top-10 z-10">
                <button @click.stop="emit('copy-text', document.text_extraction, 'full')"
                    class="btn btn-xs btn-neutral/80 mr-4" :class="{ 'btn-neutral': copiedText === 'full' }">
                    {{ copiedText === 'full' ? 'Copied!' : 'Copy' }}
                </button>
            </div>
            <pre
                class="bg-base-300/40 p-4 rounded-lg text-base-content/70 whitespace-pre-wrap font-sans">{{ document.text_extraction }}</pre>
        </div>
    </details>
</template>
