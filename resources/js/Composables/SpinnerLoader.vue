<template>
    <span class="loader"></span>
</template>
<style>

.loader {
    min-width: 22px;
    min-height: 22px;
    border: 3px solid #0f172b;
    border-bottom-color: #ff4405;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}
.dark .loader {
    border: 3px solid #ffffff;
    border-bottom-color: #ff4405;
}

@keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
