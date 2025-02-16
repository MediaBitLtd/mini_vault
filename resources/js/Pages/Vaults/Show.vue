<template>
    <h1>Hi from vault {{ vault.name }}</h1>
</template>
<script setup lang="ts">
import { VaultResource } from '~/types/resources'

const props = defineProps<{
    vault: VaultResource;
}>()
</script>
