<template>
    <span class="loader"></span>
</template>
<style>

.loader {
    min-width: 22px;
    min-height: 22px;
    border: 4px solid #0f172b;
    border-bottom-color: #ff3d00;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}
.dark .loader {
    border: 4px solid #ffffff;
    border-bottom-color: #ff3d00;
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
