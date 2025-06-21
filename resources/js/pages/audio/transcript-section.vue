<script setup>
const { audio, copiedText, isTranscriptOpen } = defineProps({
    audio: {
        type: Object,
        default: null
    },
    copiedText: String,
    isTranscriptOpen: Boolean
})

const emit = defineEmits(['copy-text'])
</script>

<template>
    <details class="collapse collapse-arrow bg-base-100 border-base-300 border rounded-2xl" :open="isTranscriptOpen">
        <summary class="collapse-title font-semibold">transcript</summary>
        <div class="collapse-content relative">
            <div class="absolute right-12 -top-10 z-10">
                <button @click.stop="emit('copy-text', audio.transcript, 'transcript')" class="btn btn-xs btn-neutral/80 mr-4"
                    :class="{ 'btn-neutral': copiedText === 'transcript' }">
                    {{ copiedText === 'transcript' ? 'Copied!' : 'Copy' }}
                </button>
                <a :href="`/audio/results/${audio.id}/transcript/download`" class="btn btn-xs btn-primary">
                    Download
                </a>
            </div>
            <pre
                class="bg-base-300/40 p-4 rounded-lg text-base-content/80 whitespace-pre-wrap font-sans">{{ audio.transcript }}</pre>
        </div>
    </details>
</template>
