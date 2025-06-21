<script setup>
const { document, copiedText, isTranslationOpen } = defineProps({
    document: {
        type: Object,
        default: null
    },
    copiedText: String,
    isTranslationOpen: Boolean,
})

const emit = defineEmits(['copy-text'])
</script>

<template>
    <details class="collapse collapse-arrow bg-base-100 border-base-300 border rounded-2xl" :open="isTranslationOpen">
        <summary class="collapse-title font-semibold">Translate</summary>
        <div class="collapse-content relative">
            <div class="absolute right-12 -top-10 z-10">
                <button @click.stop="emit('copy-text', document.translations, 'translation')" class="btn btn-xs btn-neutral/80 mr-4"
                    :class="{ 'btn-neutral': copiedText === 'translation' }">
                    {{ copiedText === 'translation' ? 'Copied!' : 'Copy' }}
                </button>
                <a :href="`/document/results/${document.id}/translation/download`" class="btn btn-xs btn-primary">
                    Download
                </a>
            </div>
            <pre
                class="bg-base-300/40 p-4 rounded-lg text-base-content/80 whitespace-pre-wrap font-sans">{{ document.translations }}</pre>
        </div>
    </details>
</template>
