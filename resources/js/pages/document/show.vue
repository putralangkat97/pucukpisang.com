<script setup>
import { ref } from 'vue';
import AppLayout from '../../layouts/app-layout.vue';
import { Link } from '@inertiajs/vue3';
import SummarySection from './summary-section.vue';
import TranslateSection from './translate-section.vue';
import OriginalTextSection from './original-text-section.vue';

const props = defineProps({
    document: Object,
});

const copiedText = ref('');
const isSummaryOpen = ref(true);
const isTranslationOpen = ref(true);
const isFullTextOpen = ref(false);

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
            'h-auto mt-32': isSummaryOpen || isTranslationOpen || isFullTextOpen,
        }">
            <div class="hero-content w-full max-w-4xl">
                <div class="card w-full bg-base-200 rounded-2xl shadow-xl">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h1 class="card-title text-3xl">Document Results</h1>
                            <Link href="/document" class="btn btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                class="size-5">
                                <path fill="currentColor"
                                    d="M20 11H7.83l5.59-5.59L12 4l-8 8l8 8l1.41-1.41L7.83 13H20z" />
                            </svg>
                            Process Another
                            </Link>
                        </div>
                        <p class="text-base-content/60 mb-4">
                            Processed with AI Model:
                            <span class="font-mono badge badge-sm badge-neutral ml-2">{{ document.ai_model }}</span>
                        </p>

                        <!-- Summary Section -->
                        <div v-if="document.summary" class="divider"></div>
                        <SummarySection v-if="document.summary" :document="document" :copiedText="copiedText"
                            :isSummaryOpen="isSummaryOpen" @copy-text="copyToClipboard" />

                        <!-- Translation Section -->
                        <div v-if="document.translations" class="divider"></div>
                        <TranslateSection v-if="document.translations" :document="document" :copiedText="copiedText"
                            :isTranslationOpen="isTranslationOpen" @copy-text="copyToClipboard" />

                        <!-- Full Text Section -->
                        <div v-if="document.text_extraction" class="divider"></div>
                        <OriginalTextSection v-if="document.text_extraction" :document="document"
                            :copiedText="copiedText" :isFullTextOpen="isFullTextOpen" @copy-text="copyToClipboard" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
