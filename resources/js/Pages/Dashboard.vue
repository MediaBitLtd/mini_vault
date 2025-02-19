<template>
    <h1 class="text-3xl font-bold mb-10">Dashboard</h1>
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
<script setup lang="ts">
import { Card } from 'primevue'
</script>
