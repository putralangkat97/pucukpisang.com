<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, onUnmounted, ref } from 'vue';
import Stepper from './stepper.vue';

const {aiModels, summaryLengths, languages} = defineProps({
    aiModels: Array,
    summaryLengths: Array,
    languages: Array,
})

const currentStep = ref(0);
const processId = ref(null);
const currentStatusMessage = ref('');
const isProcessing = ref(false);
const isFinished = ref(false);
const isDragging = ref(false);
const resultLink = ref('#');
const fileInput = ref(null);

let pollingInterval = null;

const isTranslateSelected = computed(() => form.operations.includes('translate'));
const isSummarizeSelected = computed(() => form.operations.includes('summarize'));

const form = useForm({
    document_file: null,
    operations: [],
    target_language: 'es',
    summary_length: '',
    ai_model: '',
});

const onDrop = (event) => {
    isDragging.value = false;
    form.document_file = event.dataTransfer.files[0];
}

const onFileSelect = (event) => {
    form.document_file = event.target.files[0];
}

const triggerFileInput = () => {
    fileInput.value.click();
}

const fileSizeFormatted = computed(() => {
    if (!form.document_file) return '';
    const size = form.document_file.size;
    if (size > 1024 * 1024) {
        return `${(size / (1024 * 1024)).toFixed(2)} MB`;
    }
    return `${(size / 1024).toFixed(2)} KB`;
});

const resetForm = () => {
    form.reset();
    processId.value = null;
    currentStep.value = 0;
    currentStatusMessage.value = '';
    isProcessing.value = false;
    isFinished.value = false;
    if (pollingInterval) clearInterval(pollingInterval);
}

const startProcessing = () => {
    isProcessing.value = true;
    currentStep.value = 0;

    const formData = new FormData();
    formData.append('document_file', form.document_file);
    formData.append('ai_model', form.ai_model);

    if (form.operations.includes('summarize')) {
        formData.append('operations[summarize][length]', form.summary_length);
    }
    if (form.operations.includes('translate')) {
        formData.append('operations[translate][language]', form.target_language);
    }

    axios.post('/api/documents', formData)
        .then((response) => {
            console.log('nunggu response', response)
            processId.value = response.data.id;
            poll();
            pollingInterval = setInterval(poll, 3000);
        })
        .catch(error => {
            console.error('Submission failed:', error.response?.data);
            alert('Submission failed: ' + (error.response?.data?.message || 'Check console.'));
            isProcessing.value = false;
        });
}

const handleSubmit = () => {
    if (!form.document_file) {
        alert('Please select a file first.');
        return;
    }

    startProcessing();
}

const poll = () => {
    if (!processId.value) return; // a safety check

    axios.get(`/api/documents/${processId.value}`)
        .then(response => {
            const doc = response.data;
            currentStep.value = doc.status;
            currentStatusMessage.value = doc.message;

            if (doc.status === 98) { // COMPLETE
                clearInterval(pollingInterval);
                isProcessing.value = false;
                isFinished.value = true;
                resultLink.value = `/document/results/${doc.id}`;
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
}

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});
</script>

<template>
    <div class="hero min-h-screen bg-base-300 -mt-28">
        <div class="hero-content flex flex-col w-full max-w-3xl px-4">
            <div v-if="!processId" class="card w-full bg-base-200 shadow-xl">
                <form @submit.prevent="handleSubmit" class="card-body space-y-4">
                    <h2 class="card-title text-2xl justify-center">Process Your Document</h2>

                    <input type="file" ref="fileInput" @change="onFileSelect" class="hidden"
                        accept=".pdf,.jpg,.jpeg,.png,.webp" />

                    <!-- drop zone -->
                    <div @dragover.prevent @dragenter.prevent="isDragging = true" @dragleave="isDragging = false"
                        @drop.prevent="onDrop" @click="triggerFileInput" :class="[
                            'border-2 border-dashed rounded-lg p-10 text-center cursor-pointer transition-colors duration-300',
                            isDragging ? 'border-primary bg-primary/20' : 'border-base-content/30 hover:border-primary'
                        ]">

                        <!-- when no file is selected -->
                        <div v-if="!form.document_file" class="flex flex-col items-center justify-center space-y-4">
                            <svg class="w-12 h-12 text-base-content/50" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M3 17.25V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 17.25z" />
                            </svg>
                            <p class="text-lg font-semibold">
                                <span class="text-primary">Click to upload</span> or
                                drag and drop
                            </p>
                            <p class="text-sm text-base-content/60">PDF, JPG, PNG (MAX. 10MB)</p>
                        </div>

                        <!-- when file selected -->
                        <div v-else class="flex flex-col items-center justify-center space-y-3">
                            <svg class="w-12 h-12 text-success opacity-80" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <p class="font-semibold text-lg">{{ form.document_file.name }}</p>
                            <p class="text-sm text-base-content/80 font-mono badge badge-outline">
                                {{ fileSizeFormatted }}
                            </p>
                            <button @click.stop="resetForm"
                                class="btn btn-sm btn-ghost text-error mt-2 opacity-70 hover:opacity-100 transition-opacity">
                                Remove File
                            </button>
                        </div>
                    </div>

                    <!-- operations -->
                    <div class="flex space-x-10 text-left">
                        <h3 class="font-bold text-lg">Operations</h3>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-4">
                                <input type="checkbox" v-model="form.operations" value="summarize"
                                    class="toggle toggle-primary" />
                                <span class="label-text">Summarize</span>
                            </label>
                        </div>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-4">
                                <input type="checkbox" v-model="form.operations" value="translate"
                                    class="toggle toggle-primary" />
                                <span class="label-text">Translate</span>
                            </label>
                        </div>
                    </div>

                    <!-- options for summarize/translate -->
                    <div v-if="isSummarizeSelected || isTranslateSelected" class="grid gap-4 text-left"
                        :class="{ 'grid-cols-1': isSummarizeSelected || isTranslateSelected, 'grid-cols-2': isSummarizeSelected && isTranslateSelected }">
                        <div v-if="isSummarizeSelected" class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold">Summarization Length</span>
                            </label>
                            <select v-model="form.summary_length" class="select select-bordered w-full mt-1">
                                <option value="" selected disabled>Choose</option>
                                <option v-for="len in summaryLengths" :key="len.value" :value="len.value">
                                    {{ len.text }}
                                </option>
                            </select>
                        </div>
                        <div v-if="isTranslateSelected" class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-semibold">Translate To</span>
                            </label>
                            <select v-model="form.target_language" class="select select-bordered w-full mt-1">
                                <option value="" disabled selected>choose</option>
                                <option v-for="lang in languages" :key="lang.value" :value="lang.value">
                                    {{ lang.text }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- AI Model Selection -->
                    <div class="text-left">
                        <h3 class="font-bold text-lg">AI Model</h3>
                        <div class="form-control w-full">
                            <select v-model="form.ai_model" class="select select-bordered w-full mt-1">
                                <option value="" disabled selected>Choose</option>
                                <option v-for="model in aiModels" :key="model.value" :value="model.value">
                                    {{ model.text }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary w-full"
                            :disabled="!form.document_file || form.operations.length === 0 || isProcessing">
                            <span v-if="isProcessing" class="loading loading-spinner"></span>
                            <span v-else>Process Document</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- stepper -->
            <Stepper v-else :currentStep="currentStep" :isProcessing="isProcessing" :isFinished="isFinished"
                :resultLink="resultLink" :currentStatusMessage="currentStatusMessage"
                @reset-document-form="resetForm" />
        </div>
    </div>
</template>
