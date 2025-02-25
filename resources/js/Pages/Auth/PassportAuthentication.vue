<template>
    <AuthLayout>
        <div class="w-full md:min-w-72 space-y-4">
            <h3 class="text-gray-800 text-3xl text-center font-extrabold mb-8 dark:text-zinc-200">
                Mini Vault
            </h3>

            <p>
                <strong>{{ client.name }}</strong> is requesting permission to access your account.
            </p>

            <div v-if="scopes.length" class="my-10">
                <p class="text-gray-500 dark:text-gray-400 font-bold mb-2">This application will be able to:</p>

                <ul class="space-y-2">
                    <li v-for="scope in scopes" class="flex items-center gap-2 font-bold">
                        <i class="pi pi-caret-right" />{{ scope.description }}
                    </li>
                </ul>
            </div>

            <form ref="form" method="post">
                <input type="hidden" name="_token" :value="props.csrf" />
                <input type="hidden" name="state" :value="props.state" />
                <input type="hidden" name="auth_token" :value="props.authToken" />
                <input type="hidden" name="client_id" :value="client.id" />
            </form>

            <div class="flex flex-col gap-3">
                <Button severity="secondary" fluid @click="reject">Reject</Button>
                <Button severity="contrast" fluid @click="accept">Accept</Button>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup lang="ts">
import AuthLayout from '~/Layouts/AuthLayout.vue'
import { Button } from 'primevue'
import { computed, ref } from 'vue'

const props = defineProps<{
    csrf: string;
    client: string;
    scopes: string;
    authToken: string;
    state: string;
    approveEndpoint: string;
    denyEndpoint: string;
}>()

const form = ref<HTMLFormElement>()

const client = computed(() => JSON.parse(props.client))
const scopes = computed(() => JSON.parse(props.scopes))

const accept = () => {
    form.value.action = props.approveEndpoint
    form.value.submit()
}
const reject = () => {
    form.value.action = props.denyEndpoint
    const deleteInput = document.createElement('input')
    deleteInput.name = '_method'
    deleteInput.value = 'delete'
    form.value.appendChild(deleteInput)

    form.value.submit()
}
</script>
