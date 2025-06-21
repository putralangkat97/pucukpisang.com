<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, onUnmounted, ref } from 'vue';
import Stepper from './stepper.vue';

const { aiModels, summaryLengths, languages } = defineProps({
    aiModels: Array,
    summaryLengths: Array,
    languages: Array,
});

const processId = ref(null);
const currentStep = ref(0);
const currentStatusMessage = ref('');
const isProcessing = ref(false);
const isFinished = ref(false);
const isDragging = ref(false);
const resultLink = ref('#');
const fileInput = ref(null);
let pollingInterval = null;

const form = useForm({
    source_type: 'upload',
    audio_file: null,
    youtube_url: '',
    operations: [],
    ai_model: '',
    summary_length: '',
    target_language: 'es',
});

// --- Computed Properties ---
const isTranslateSelected = computed(() => form.operations.includes('translate'));
const isSummarizeSelected = computed(() => form.operations.includes('summarize'));
const fileSizeFormatted = computed(() => {
    if (!form.audio_file) return '';
    const sizeInMb = form.audio_file.size / (1024 * 1024);
    return `${sizeInMb.toFixed(2)} MB`;
});

const onDrop = (event) => {
    isDragging.value = false;
    form.audio_file = event.dataTransfer.files[0];
};

const onFileSelect = (event) => {
    form.audio_file = event.target.files[0];
};

const triggerFileInput = () => {
    fileInput.value.click();
};

const resetForm = () => {
    form.reset();
    processId.value = null;
    currentStep.value = 0;
    currentStatusMessage.value = '';
    isProcessing.value = false;
    isFinished.value = false;
    if (pollingInterval) clearInterval(pollingInterval);
};

const handleSubmit = () => {
    if (form.source_type === 'upload' && !form.audio_file) {
        alert('Please select an audio file to upload.');
        return;
    }
    if (form.source_type === 'youtube' && form.youtube_url.trim() === '') {
        alert('Please enter a valid YouTube URL.');
        return;
    }
    if (form.operations.length > 0 && form.ai_model === '') {
        alert('Please select an AI model for Summarize/Translate operations.');
        return;
    }

    isProcessing.value = true;
    currentStep.value = 0;

    const formData = new FormData();
    formData.append('source_type', form.source_type);
    formData.append('ai_model', form.ai_model);

    if (form.source_type === 'upload') {
        formData.append('audio_file', form.audio_file);
    } else {
        formData.append('youtube_url', form.youtube_url);
    }

    if (form.source_type === 'upload') {
        formData.append('audio_file', form.audio_file);
    } else {
        formData.append('youtube_url', form.youtube_url);
    }

    if (isSummarizeSelected.value) {
        formData.append('operations[summarize][length]', form.summary_length);
    }
    if (isTranslateSelected.value) {
        formData.append('operations[translate][language]', form.target_language);
    }

    axios.post('/api/audio', formData)
        .then((response) => {
            processId.value = response.data.id;
            poll();
            pollingInterval = setInterval(poll, 3000);
        })
        .catch(error => {
            console.error('Submission failed:', error.response?.data);
            alert('Submission failed: ' + (error.response?.data?.message || 'Check console.'));
            isProcessing.value = false;
        });
};

const poll = () => {
    if (!processId.value) return;
    axios.get(`/api/audio/${processId.value}`)
        .then(response => {
            const doc = response.data;
            currentStep.value = doc.status;
            currentStatusMessage.value = doc.message;
            if (doc.status === 98) {
                clearInterval(pollingInterval);
                isProcessing.value = false;
                isFinished.value = true;
                resultLink.value = `/audio/results/${doc.id}`;
            }
            if (doc.status === 99) { // ERRORED
                clearInterval(pollingInterval);
                isProcessing.value = false;
                currentStatusMessage.value = `Error: ${doc.error}`;
            }
        })
        .catch(error => {
            console.error('Polling failed:', error);
            clearInterval(pollingInterval);
            isProcessing.value = false;
        });
};

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});
</script>

<template>
    <div class="hero min-h-screen bg-base-300 -mt-28">
        <div class="hero-content flex flex-col w-full max-w-3xl px-4">
            <div v-if="!processId" class="card w-full bg-base-200 shadow-xl">
                <form @submit.prevent="handleSubmit" class="card-body space-y-4">
                    <h2 class="card-title text-2xl justify-center">Transcribe Your Audio</h2>

                    <!-- NEW: Explicit Source Selection -->
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-lg">1. Select Source</span></label>
                        <div class="flex gap-4 p-2 rounded-box">
                            <label class="label cursor-pointer gap-2">
                                <input type="radio" v-model="form.source_type" value="upload"
                                    class="radio radio-primary" />
                                <span class="label-text">File Upload</span>
                            </label>
                            <label class="label cursor-pointer gap-2">
                                <input type="radio" v-model="form.source_type" value="youtube"
                                    class="radio radio-primary" />
                                <span class="label-text">YouTube URL</span>
                            </label>
                        </div>
                    </div>

                    <input type="file" ref="fileInput" @change="onFileSelect" class="hidden"
                        accept=".mp3, .mp4, .mpeg, .wav, .webm" />

                    <!-- IMPROVEMENT: Conditional Drop Zone based on source_type -->
                    <div v-if="form.source_type === 'upload'">
                        <div @dragover.prevent @dragenter.prevent="isDragging = true" @dragleave="isDragging = false"
                            @drop.prevent="onDrop" @click="triggerFileInput" :class="[
                                'border-2 border-dashed rounded-lg p-10 text-center cursor-pointer transition-colors duration-300',
                                isDragging ? 'border-primary bg-primary/20' : 'border-base-content/30 hover:border-primary'
                            ]">
                            <!-- when no file is selected -->
                            <div v-if="!form.audio_file" class="flex flex-col items-center justify-center space-y-4">
                                <svg class="w-12 h-12 text-base-content/50" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M3 17.25V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 17.25z" />
                                </svg>
                                <p class="text-lg font-semibold"><span class="text-primary">Click to upload</span> or
                                    drag and drop</p>
                                <p class="text-sm text-base-content/60">MP3, MP4, WAV (MAX. 25MB)</p>
                            </div>
                            <!-- when file selected -->
                            <div v-else class="flex flex-col items-center justify-center space-y-3">
                                <svg class="w-12 h-12 text-success opacity-80" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-semibold text-lg">{{ form.audio_file.name }}</p>
                                <p class="text-sm text-base-content/80 font-mono badge badge-outline">{{
                                    fileSizeFormatted }}</p>
                                <button @click.stop="resetForm"
                                    class="btn btn-sm btn-ghost text-error mt-2 opacity-70 hover:opacity-100 transition-opacity">Remove
                                    File</button>
                            </div>
                        </div>
                    </div>

                    <!-- YouTube URL Input -->
                    <div v-if="form.source_type === 'youtube'" class="form-control">
                        <input type="url" v-model="form.youtube_url" class="input input-bordered w-full block"
                            placeholder="https://www.youtube.com/watch?v=..." />
                    </div>

                    <!-- IMPROVEMENT: Simplified Operations -->
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-lg">2. Additional
                                Operations</span></label>
                        <div class="flex gap-4 p-2">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" v-model="form.operations" value="summarize"
                                    class="checkbox checkbox-primary" />
                                <span class="label-text">Summarize</span>
                            </label>
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" v-model="form.operations" value="translate"
                                    class="checkbox checkbox-primary" />
                                <span class="label-text">Translate</span>
                            </label>
                        </div>
                    </div>

                    <!-- Options for Summarize/Translate -->
                    <div v-if="isSummarizeSelected || isTranslateSelected" class="grid gap-4 text-left"
                        :class="{ 'grid-cols-2': isSummarizeSelected && isTranslateSelected }">
                        <div v-if="isSummarizeSelected" class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold">Summarization
                                    Length</span></label>
                            <!-- FIX: Use `props.summaryLengths` -->
                            <select v-model="form.summary_length" class="select select-bordered w-full mt-1">
                                <option value="" selected disabled>Choose length...</option>
                                <option v-for="len in summaryLengths" :key="len.value" :value="len.value">{{
                                    len.text }}</option>
                            </select>
                        </div>
                        <div v-if="isTranslateSelected" class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold">Translate To</span></label>
                            <select v-model="form.target_language" class="select select-bordered w-full mt-1">
                                <option v-for="lang in languages" :key="lang.value" :value="lang.value">{{
                                    lang.text }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- AI Model Selection -->
                    <div class="text-left" v-if="isSummarizeSelected || isTranslateSelected">
                        <label class="label"><span class="label-text font-bold text-lg">3. AI Model</span></label>
                        <div class="form-control w-full">
                            <!-- FIX: Use `props.aiModels` -->
                            <select v-model="form.ai_model" class="select select-bordered w-full mt-1">
                                <option value="" disabled selected>Choose model...</option>
                                <option v-for="model in aiModels" :key="model.value" :value="model.value">{{
                                    model.text }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary w-full"
                            :disabled="(form.source_type === 'upload' && !form.audio_file) || (form.source_type === 'youtube' && form.youtube_url.trim() === '') || isProcessing">
                            <span v-if="isProcessing" class="loading loading-spinner"></span>
                            <span v-else>Process Audio</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- stepper -->
            <Stepper v-else :currentStep="currentStep" :isProcessing="isProcessing" :isFinished="isFinished"
                :resultLink="resultLink" :currentStatusMessage="currentStatusMessage" @reset-audio-form="resetForm" />
        </div>
    </div>
</template>
