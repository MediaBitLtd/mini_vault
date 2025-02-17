<template>
    <h1>Hi from vault {{ vault.name }}</h1>
    <Button severity="danger" @click="deleteVault">Delete vault</Button>
</template>
<script setup lang="ts">
import { VaultResource } from '~/types/resources'
import { useConfirm, Button } from 'primevue'
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useVaults } from '~/Composables/vaults'

const props = defineProps<{
    vault: VaultResource;
}>()

const form = useForm();
const confirm = useConfirm()
const { loadVaults } = useVaults()

const confirmMessage = computed(() =>
    `All records will be lost for ever. Are you sure you want to delete ${ props.vault.name }?`
)

const deleteVault = () => {
    confirm.require({
        message: confirmMessage.value,
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
        },
        accept: async () => {
            form.delete(`/vault/${props.vault.id}`, {
                onSuccess() {
                    loadVaults()
                }
            })
        },
    })
}
</script>
