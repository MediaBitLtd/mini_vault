<template>
    <h1>Hi {{ user.first_name }}!</h1>
</template>
<script setup lang="ts">
import { useAuth } from '~/Composables/useAuth'

const { user } = useAuth()
</script>
