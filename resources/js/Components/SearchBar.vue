<template>
    <IconField>
        <InputText fluid v-model="search" placeholder="Search" :disabled />
        <InputIcon class="pi pi-search" />
    </IconField>
</template>
<script setup lang="ts">
import { IconField, InputText, InputIcon } from 'primevue'
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue?: string;
    disabled?: boolean;
}>()

const emit = defineEmits(['update:modelValue']);

const search = ref(props.modelValue || '');

watch(
    () => search.value,
    () => emit('update:modelValue', search.value),
)

</script>
