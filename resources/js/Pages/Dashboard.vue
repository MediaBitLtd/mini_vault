<template>
    <h1>Hi {{ user.first_name }}!</h1>
    <a href="/auth/logout">Logout</a>
</template>
<script setup lang="ts">
import { useAuth } from '~/Composables/useAuth'

const { user } = useAuth()
</script>
