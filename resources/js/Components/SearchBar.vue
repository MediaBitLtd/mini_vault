<template>
    <IconField>
        <InputText fluid v-model="search" placeholder="Search" />
        <InputIcon class="pi pi-search" />
    </IconField>
</template>
<script setup lang="ts">
import { IconField, InputText, InputIcon } from 'primevue'
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue?: string;
}>()

const emit = defineEmits(['update:modelValue']);

const search = ref(props.modelValue || '');

watch(
    () => search.value,
    () => emit('update:modelValue', search.value),
)

</script>
