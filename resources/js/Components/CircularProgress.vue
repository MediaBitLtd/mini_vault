<template>
    <svg width="22" height="22" viewBox="0 0 22 22" class="circular-progress">
        <circle class="bg"></circle>
        <circle class="fg"></circle>
    </svg>
</template>
<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    modelValue: number;
    inverted?: boolean;
}>()

const progress = computed(() => props.inverted ? 100 - props.modelValue : props.modelValue)
const rotate = computed(() => props.inverted ? 'rotateY(180deg)' : 'none')
</script>
<style scoped>
.circular-progress {
    --size: 22px;
    --half-size: calc(var(--size) / 2);
    --stroke-width: 3px;
    --radius: calc((var(--size) - var(--stroke-width)) / 2);
    --circumference: calc(var(--radius) * pi * 2);
    --dash: calc((var(--progress) * var(--circumference)) / 100);
    --progress: v-bind('progress');
    transform: v-bind('rotate');
}

.circular-progress circle {
    cx: var(--half-size);
    cy: var(--half-size);
    r: var(--radius);
    stroke-width: var(--stroke-width);
    fill: none;
    stroke-linecap: round;
}

.circular-progress circle.bg {
    stroke: #ddd;
}

.dark .circular-progress circle.bg {
    stroke: #fff;
}

.circular-progress circle.fg {
    transform: rotate(-90deg);
    transform-origin: var(--half-size) var(--half-size);
    stroke-dasharray: var(--dash) calc(var(--circumference) - var(--dash));
    transition: stroke-dasharray 0.3s linear 0s;
    stroke: #ff4405;
}
</style>
