<script setup>
import AppLayout from '../../layouts/app-layout.vue';
import FormDocument from '../../components/app/document/form-document.vue';

const { ai_models, summary_length, languages } = defineProps({
    ai_models: Array,
    summary_length: Array,
    languages: Array,
});
</script>

<template>
    <AppLayout>
        <FormDocument :aiModels="ai_models" :summaryLengths="summary_length" :languages="languages" />
    </AppLayout>
</template>
