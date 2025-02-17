<template>
    <transition>
        <div v-if="show" class="fixed top-0 left-0 w-[100dvw] h-[100-dvh] z-[100]">
            <div class="fixed top-0 left-0 w-[100dvw] h-[100dvh] bg-black opacity-60 opacity-jump" @click="close()" />

            <div class="flex w-full h-full">
                <div class="flex flex-1 justify-center items-center p5">
                    <div class="bg-white dark:bg-gray-800 md:m-auto md:min-h-24 rounded-lg shadow z-10 grow-animation w-full h-full md:w-auto md:h-auto">
                        <div class="relative bg-gray-100 dark:bg-gray-750 border-b border-gray-300 dark:border-gray-500 rounded-t-lg p-2">
                            <h1>{{ title }}</h1>
                        </div>
                    </div>
                    <div class="modal-container overflow-auto p-4 sm:p-7 h-full">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script setup lang="ts">
import { ref, onBeforeUnmount } from 'vue'

const show = ref(false);
const title = ref('Modal');

onBeforeUnmount(() => window.onpopstate = null);

setTimeout(() => show.value = true, 1000)
</script>
<style lang="scss">
.modal-container {
    max-height: calc(100dvh - 5rem);
}

.opacity-jump {
    animation: jump 200ms;
}

.grow-animation {
    animation: grow 70ms;
}

.v-enter-active,
.v-leave-active {
    transition: opacity 80ms ease;
}

.v-leave-to {
    opacity: 0;

    .grow-animation {
        animation: shrink 70ms;
    }
}

@keyframes grow {
    from {
        width: 25rem;
        height: 12rem;
        overflow: hidden;
    }
    100% {
        overflow: auto;
    }
}

@keyframes shrink {
    to {
        overflow: hidden;
        width: 25rem;
        height: 12rem;
    }
}

@keyframes jump {
    from {
        opacity: 0;
    }
}
</style>
