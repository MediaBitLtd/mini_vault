<template>
    <Panel ref="panel" class="cursor-pointer" :header="record.name" collapsed @click="open">
        <p class="mb-10">
            Fucking awesome aye
        </p>
        <div class="flex items-center gap-4 mb-4">
            <label for="username" class="font-semibold w-24">Username</label>
            <InputText fluid id="username" class="flex-auto" autocomplete="off" />
        </div>
        <div class="flex items-center gap-4 mb-8">
            <label for="email" class="font-semibold w-24">Email</label>
            <InputText fluid id="email" class="flex-auto" autocomplete="off" />
        </div>
    </Panel>
</template>
<script setup lang="ts">
import { InputText, Panel } from 'primevue'
import { VaultRecordResource, VaultResource } from '~/types/resources'
import { ref } from 'vue'

const props = defineProps<{
    vault: VaultResource,
    record: VaultRecordResource,
}>()

const panel = ref()
const opened = ref(false);

const open = () => {
    if (opened.value) {
        return;
    }

    opened.value = true;
    panel.value.toggle();

    setTimeout(() => {
        panel.value.$el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'end' })
    }, 150)
}
</script>
