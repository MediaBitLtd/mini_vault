<template>
    <div class="container" :class="props.menuOpened ? 'opened' : ''">
        <div class="bar1 bg-black dark:bg-white rounded-xl"></div>
        <div class="bar2 bg-black dark:bg-white rounded-xl"></div>
        <div class="bar3 bg-black dark:bg-white rounded-xl"></div>
    </div>
</template>
<script lang="ts" setup>
const props = defineProps<{
    menuOpened: boolean;
}>()
</script>
<style>
.container {
    display: block;
    cursor: pointer;
}

.bar1, .bar2, .bar3 {
    width: 28px;
    height: 2px;
    margin: 7px 0;
    transition: 100ms;
}

.opened .bar1 {
    transform: translate(0, 9px) rotate(-45deg);
}

.opened .bar2 {
    opacity: 0;
}

.opened .bar3 {
    transform: translate(0, -9px) rotate(45deg);
}
</style>
