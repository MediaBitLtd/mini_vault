<template>
    <label class="switch">
        <input type="checkbox" :checked="props.modelValue" @input="emit('update:modelValue', $event.target.checked)">
        <div class="slider round">
            <div v-if="darkModeSwitch" class="flex justify-between items-center w-full h-full px-2">
                <span class="text-sm">🌙</span>
                <span class="text-sm">☀️</span>
            </div>
        </div>
    </label>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { useDarkMode } from '~/Composables/settings'

const props = defineProps<{
    modelValue: boolean;
    darkModeSwitch?: boolean;
}>()

const { darkMode } = useDarkMode()

const emit = defineEmits(['update:modelValue'])

const offColor = computed(() => {
    if (props.darkModeSwitch) {
        return '#cccccc'
    }

    if (darkMode.value) {
        return '#9f2d2d'
    }

    return '#d37f7f'
})

const onColor = computed(() => {
    if (props.darkModeSwitch) {
        return '#363636'
    }

    if (darkMode.value) {
        return '#6d8770'
    }

    return '#a3c3a7'
})
</script>

<style scoped>
.switch {
    position: relative;
    display: inline-block;
    width: 58px;
    height: 29px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: v-bind(offColor);
    -webkit-transition: 0.4s;
    transition: 0.4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 21px;
    width: 21px;
    left: 5px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: 0.4s;
    transition: 0.4s;
}

input:checked + .slider {
    background-color: v-bind(onColor);
}

input:focus + .slider {
    box-shadow: 0 0 1px v-bind(onColor);
}

input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>
