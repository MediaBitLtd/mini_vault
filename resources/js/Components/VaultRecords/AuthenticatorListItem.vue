<template>
    <div class="flex justify-between">
        <h4>{{ title }}</h4>
        <RecordValue :record-value="tfaValue" no-label />
    </div>
</template>
<script setup lang="ts">
import { VaultRecordResource } from '~/types/resources'
import RecordValue from '~/Components/VaultRecords/RecordValue.vue'
import { computed } from 'vue'

const props = defineProps<{
    record: VaultRecordResource;
}>()

const tfaValue = computed(() => props.record.values.find(value => value.field.type === '2fa'))
const appName = computed(() => props.record.values.find(value => value.field.slug === 'appname'))

const title = computed(() => appName.value?.name || props.record.name)

</script>
