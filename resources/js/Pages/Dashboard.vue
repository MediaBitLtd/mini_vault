<template>
    <h1>Hi mom!</h1>
</template>
<script setup lang="ts">
import CookieJS from 'js-cookie'
if (!CookieJS.get('_accessToken')) {
    window.location.href = '/auth/logout'
}
</script>
