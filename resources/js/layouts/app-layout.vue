<script setup>
import Footer from '../components/app/footer.vue';
import NavigationBar from '../components/app/navigation-bar.vue';

</script>

<template>
    <div class="bg-base-300 h-screen">
        <div class="flex flex-col mx-auto justify-center max-w-7xl">
            <NavigationBar />
        </div>
        <div>
            <slot />
            <Footer />
        </div>
    </div>
</template>
