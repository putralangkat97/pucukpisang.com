<script setup>
const props = defineProps({
    currentStep: Number,
    isProcessing: Boolean,
    isFinished: Boolean,
    resultLink: String,
    currentStatusMessage: String
})

const emit = defineEmits(['reset-document-form'])
</script>

<template>
    <div class="card w-full bg-base-200 shadow-xl p-8 text-center">
        <h2 v-if="!isFinished" class="text-2xl text-info font-bold mb-6">Processing Document ...</h2>
        <ul class="steps steps-vertical lg:steps-horizontal w-full">
            <li class="step"
                :class="{ 'step-success': isFinished, 'step-info': currentStep >= 1, 'step-error': currentStep === 99 }">
                Extracting Text
            </li>
            <li class="step"
                :class="{ 'step-success': isFinished, 'step-info': currentStep >= 2, 'step-error': currentStep === 99 }">
                Translating
            </li>
            <li class="step"
                :class="{ 'step-success': isFinished, 'step-info': currentStep >= 3, 'step-error': currentStep === 99 }">
                Summarizing
            </li>
            <li class="step"
                :class="{ 'step-success': isFinished, 'step-info': currentStep === 98, 'step-error': currentStep === 99 }">
                Complete
            </li>
        </ul>

        <div class="mt-4 min-h-16 flex flex-col items-center justify-center">
            <div v-if="isProcessing" class="flex items-center gap-4">
                <span class="loading loading-spinner text-primary"></span>
                <p class="text-lg font-semibold">{{ currentStatusMessage }}</p>
            </div>

            <div v-if="isFinished" class="text-center">
                <p class="text-lg font-bold text-success mb-4">Processing Complete!</p>
                <a :href="resultLink" class="btn btn-success">
                    View Your Document
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            <div v-if="currentStep === 99" class="text-center">
                <p class="text-lg font-bold text-error mb-4">An Error Occurred</p>
                <p class="bg-error/10 text-error-content p-3 rounded-md">{{ currentStatusMessage }}</p>
            </div>
        </div>

        <div class="mt-8 border-t border-base-content/10 pt-6">
            <button @click="emit('reset-document-form')" :disabled="!isFinished" class="btn btn-soft btn-neutral btn-wide">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                    viewBox="0 0 24 24" class="size-5">
                    <path fill="currentColor" d="M20 11H7.83l5.59-5.59L12 4l-8 8l8 8l1.41-1.41L7.83 13H20z" />
                </svg>
                Process Another Document
            </button>
        </div>
    </div>
</template>
