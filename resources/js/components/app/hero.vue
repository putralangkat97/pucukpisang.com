<script setup>
import { router } from '@inertiajs/vue3';
</script>

<template>
    <div class="hero min-h-screen bg-base-300 -mt-26">
        <div class="hero-content text-left">
            <div class="max-w-7xl">
                <h1 class="text-7xl font-bold mb-2 text-primary">pucukpisang</h1>
                <h1 class="text-4xl font-bold"><span class="">AI</span> Tools For Saving Your Time</h1>
                <p class="py-3">
                    pucukpisang helps you translate, transcribe, and summarize content in seconds — no technical skills
                    required.
                </p>
                <div class="flex gap-4 items-center justify-center mt-4">
                    <button
                        class="btn btn-lg bg-linear-to-bl text-primary-content from-primary to-primary/80 border-none hover:shadow-lg hover:scale-102 hover:-rotate-2 transition-all duration-200"
                        @click="router.get('/document')"
                    >
                        Get Started
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            class="size-5">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2">
                                <path stroke-dasharray="16" stroke-dashoffset="16" d="M5 12h13.5">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.2s" values="16;0" />
                                </path>
                                <path stroke-dasharray="10" stroke-dashoffset="10" d="M19 12l-5 5M19 12l-5 -5">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.2s" dur="0.2s"
                                        values="10;0" />
                                </path>
                            </g>
                        </svg>
                    </button>
                    <button class="btn btn-lg bg-linear-to-bl from-neutral to-neutral/60 text-base-content border-none hover:shadow-lg hover:scale-102 hover:rotate-1 transition-all duration-200">
                        Sign Up
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                            class="size-5">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2">
                                <path stroke-dasharray="36" stroke-dashoffset="36"
                                    d="M12 5c-3.87 0 -7 3.13 -7 7c0 3.87 3.13 7 7 7c3.87 0 7 -3.13 7 -7">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="36;0" />
                                </path>
                                <path stroke-dasharray="12" stroke-dashoffset="12" d="M13 11l7 -7">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.2s"
                                        values="12;0" />
                                </path>
                                <path stroke-dasharray="8" stroke-dashoffset="8" d="M21 3h-6M21 3v6">
                                    <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s"
                                        values="8;0" />
                                </path>
                            </g>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
