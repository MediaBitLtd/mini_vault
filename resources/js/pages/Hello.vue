<template>
    <h1 class="text-2xl">Hi {{ props.something }}</h1>
</template>
<script setup lang="ts">
const props = defineProps<{
    something: string;
}>()
</script>
