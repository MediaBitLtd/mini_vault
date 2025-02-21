<template>
    <Panel ref="panel" class="cursor-pointer" :header="title" collapsed @click="open">
        <div v-for="recordValue in record.values" class="flex items-center gap-4">
            <label :for="`record-field-${recordValue.id}`" class="font-semibold w-24">{{ recordValue.field.label }}</label>
            <InputText fluid :id="`record-field-${recordValue.id}`" class="flex-auto" autocomplete="off" />
        </div>
    </Panel>
</template>
<script setup lang="ts">
import { InputText, Panel } from 'primevue'
import { VaultRecordResource, VaultResource } from '~/types/resources'
import { computed, ref } from 'vue'

const props = defineProps<{
    vault: VaultResource,
    record: VaultRecordResource,
}>()

const panel = ref()
const opened = ref(false);

const title = computed(() => props.record.name || props.record.category.name)

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
