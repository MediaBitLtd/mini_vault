<template>
    <div ref="el">
        <div v-if="loading" class="flex items-center gap-2">
            <SpinnerLoader />
            <span class="font-bold text-gray-400 dark:text-gray-600">Loading</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import SpinnerLoader from '~/Components/SpinnerLoader.vue'
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'

const props = defineProps<{
    loading: boolean;
    lastPage: boolean;
}>()

const emit = defineEmits(['load'])

const el = ref()

const isVisible = ref(false)

const detectVisible = () => {
    const rect = el.value.getBoundingClientRect();
    isVisible.value = (rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom - 550 <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right - 550 <= (window.innerWidth || document.documentElement.clientWidth))

    if (!props.loading && !props.lastPage && isVisible.value) {
        emit('load')
    }
}

watch(
    () => props.loading,
    () => {
        detectVisible()
    },
)

onMounted(() => {
    document.getElementById('container').addEventListener('scroll', detectVisible);
    detectVisible()
})
onBeforeUnmount(() => {
    document.getElementById('container').removeEventListener('scroll', detectVisible);
})
</script>
