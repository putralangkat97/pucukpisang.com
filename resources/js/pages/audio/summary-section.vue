<script setup>
const { audio, copiedText, isSummaryOpen } = defineProps({
    audio: {
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
        <summary class="collapse-title font-semibold">Summary</summary>
        <div class="collapse-content relative">
            <div class="absolute right-12 -top-10 z-10">
                <button @click.stop="emit('copy-text', audio.summary, 'summary')" class="btn btn-xs btn-neutral/80 mr-4"
                    :class="{ 'btn-neutral': copiedText === 'summary' }">
                    {{ copiedText === 'summary' ? 'Copied!' : 'Copy' }}
                </button>
                <a :href="`/audio/results/${audio.id}/summary/download`" class="btn btn-xs btn-primary">
                    Download
                </a>
            </div>
            <pre
                class="bg-base-300/40 p-4 rounded-lg text-base-content/80 whitespace-pre-wrap font-sans">{{ audio.summary }}</pre>
        </div>
    </details>
</template>
