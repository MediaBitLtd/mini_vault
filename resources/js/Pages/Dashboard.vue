<template>
    <PageLayout>
        <template #title>Dashboard</template>
        <template #content>
            <div class="flex flex-col gap-4">
                <Card>
                    <template #title>Authenticator</template>
                    <template #content>
                        <ul>
                            <li>Favourite #1</li>
                            <li>Favourite #2</li>
                        </ul>
                    </template>
                </Card>
                <Card>
                    <template #title>Items</template>
                    <template #content>
                        <ul>
                            <li>Favourite #1</li>
                            <li>Favourite #2</li>
                        </ul>
                    </template>
                </Card>
                <Card>
                    <template #title>Add new record</template>
                    <template #content>
                        <ul>
                            <li>Favourite #1</li>
                            <li>Favourite #2</li>
                        </ul>
                    </template>
                </Card>
            </div>
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import { Card } from 'primevue'
import PageLayout from '~/Layouts/PageLayout.vue'
</script>
