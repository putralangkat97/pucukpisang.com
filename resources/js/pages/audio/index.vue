<script setup>
import AppLayout from '../../layouts/app-layout.vue';
import FormAudio from '../../components/app/audio/form-audio.vue';

const { ai_models, summary_length, languages } = defineProps({
    ai_models: Array,
    summary_length: Array,
    languages: Array,
});
</script>

<template>
    <AppLayout>
        <FormAudio :aiModels="ai_models" :summaryLengths="summary_length" :languages="languages" />
    </AppLayout>
</template>
