<script setup>
const { document, copiedText, isSummaryOpen } = defineProps({
    document: {
        type: Object,
        default: null
    },
    copiedText: String,
    isSummaryOpen: Boolean
})
const emit = defineEmits(['copy-text'])
</script>

<template>
    <details class="collapse collapse-arrow bg-base-100 border-base-300 border rounded-2xl" :open="isSummaryOpen">
        <summary class="collapse-title font-semibold">
            Summary
            <span class="font-mono badge badge-sm ml-2" :class="{
                'badge-neutral': document.options.summarize.length === 'short',
                'badge-secondary': document.options.summarize.length === 'medium',
                'badge-primary': document.options.summarize.length === 'long',
            }">
                {{ document.options.summarize.length }}
            </span>
            <span v-if="document?.options?.translate?.language" class="font-mono badge badge-sm badge-accent ml-2">
                {{ document.options.translate.language }}
            </span>
        </summary>
        <div class="collapse-content relative">
            <div class="absolute right-12 -top-10 z-10">
                <button @click.stop="emit('copy-text', document.summary, 'summary')"
                    class="btn btn-xs btn-neutral/80 mr-4" :class="{ 'btn-neutral': copiedText === 'summary' }">
                    {{ copiedText === 'summary' ? 'Copied!' : 'Copy' }}
                </button>
                <a :href="`/document/results/${document.id}/summary/download`" class="btn btn-xs btn-primary">
                    Download
                </a>
            </div>
            <pre
                class="bg-base-300/40 p-4 rounded-lg text-base-content/80 whitespace-pre-wrap font-sans">{{ document.summary }}</pre>
        </div>
    </details>
</template>
