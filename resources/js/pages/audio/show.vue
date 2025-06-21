<script setup>
import { ref } from 'vue';
import AppLayout from '../../layouts/app-layout.vue';
import { Link } from '@inertiajs/vue3';
import SummarySection from './summary-section.vue';
import TranslateSection from './translate-section.vue';
import TranscriptSection from './transcript-section.vue';

const props = defineProps({
    audio: {
        type: Object,
        default: null
    },
});

const copiedText = ref('');
const isTranscriptOpen = ref(true)
const isSummaryOpen = ref(true);
const isTranslationOpen = ref(true);

const copyToClipboard = (text, type) => {
    navigator.clipboard.writeText(text).then(() => {
        copiedText.value = type;
        setTimeout(() => {
            copiedText.value = '';
        }, 2000);
    });
}
</script>

<template>
    <AppLayout>
        <div class="hero bg-base-300" :class="{
            'h-auto mt-32': isSummaryOpen || isTranslationOpen || isTranscriptOpen,
        }">
            <div class="hero-content w-full max-w-4xl">
                <div class="card w-full bg-base-200 rounded-2xl shadow-xl">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h1 class="card-title text-3xl">Audio Results</h1>
                            <Link href="/audio" class="btn btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                class="size-5">
                                <path fill="currentColor"
                                    d="M20 11H7.83l5.59-5.59L12 4l-8 8l8 8l1.41-1.41L7.83 13H20z" />
                            </svg>
                            Process Another Audio
                            </Link>
                        </div>
                        <p class="text-base-content/60 mb-4">
                            Processed with AI Model:
                            <span class="font-mono badge badge-sm badge-neutral ml-2">{{ audio.ai_model }}</span>
                        </p>

                        <!-- Transcribe Section -->
                        <div v-if="audio.transcript" class="divider"></div>
                        <TranscriptSection v-if="audio.transcript" :audio="audio" :copiedText="copiedText"
                            :isTranscriptOpen="isTranscriptOpen" @copy-text="copyToClipboard" />

                        <!-- Summary Section -->
                        <div v-if="audio.summary" class="divider"></div>
                        <SummarySection v-if="audio.summary" :audio="audio" :copiedText="copiedText"
                            :isSummaryOpen="isSummaryOpen" @copy-text="copyToClipboard" />

                        <!-- Translation Section -->
                        <div v-if="audio.translations" class="divider"></div>
                        <TranslateSection v-if="audio.translations" :audio="audio" :copiedText="copiedText"
                            :isTranslationOpen="isTranslationOpen" @copy-text="copyToClipboard" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
