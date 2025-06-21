<script setup>
import AppLayout from '../../layouts/app-layout.vue';
import FormDocument from '../../components/app/document/form-document.vue';

const languages = [
    { value: 'de', text: '🇩🇪 German' },
    { value: 'es', text: '🇪🇸 Spanish' },
    { value: 'zh', text: '🇨🇳 Chinese' },
    { value: 'ja', text: '🇯🇵 Japanese' },
    { value: 'id', text: '🇮🇩 Indonesian' },
    { value: 'en', text: '🇬🇧 English' },
];

const summaryLengths = [
    { value: 'short', text: 'Short (a few sentences)' },
    { value: 'medium', text: 'Medium (a paragraph)' },
    { value: 'long', text: 'Long (multiple paragraphs)' },
];

const aiModels = [
    { value: 'gemini', text: '🤖 Google (Gemini)' },
    { value: 'openai', text: '🧠 OpenAI (GPT)' },
    { value: 'deepseek', text: '🔍 DeepSeek' },
];
</script>

<template>
    <AppLayout>
        <FormDocument :aiModels="aiModels" :summaryLengths="summaryLengths" :languages="languages" />
    </AppLayout>
</template>
