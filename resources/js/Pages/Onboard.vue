<template>
    <div class="flex flex-col justify-between h-[90dvh] min-w-76 md:h-auto min-h-90">
        <WelcomeStep v-show="activeStep === 'welcome'" />
        <PasswordStep ref="passwordStep" v-show="activeStep === 'password'" @next="nextStep(true)" />
        <CreateVaultStep ref="vaultStep" v-show="activeStep === 'vaults'" @next="nextStep(true)" />
        <BiometricStep v-show="activeStep === 'biometrics'" />
        <FinishStep v-show="activeStep === 'finish'" />
        <div v-if="!loadingVaults" class="space-y-4">
            <div class="flex justify-center gap-6">
                <div v-if="steps.welcome.enabled" class="rounded-full w-3 h-3 bg-orange-500"></div>
                <div v-if="steps.password.enabled" class="rounded-full w-3 h-3" :class="steps.password.active ? 'bg-orange-500' : 'bg-slate-400 dark:bg-slate-800'"></div>
                <div v-if="steps.vaults.enabled" class="rounded-full w-3 h-3" :class="steps.vaults.active ? 'bg-orange-500' : 'bg-slate-400 dark:bg-slate-800'"></div>
                <div v-if="steps.biometrics.enabled" class="rounded-full w-3 h-3" :class="steps.biometrics.active ? 'bg-orange-500' : 'bg-slate-400 dark:bg-slate-800'"></div>
                <div v-if="steps.finish.enabled" class="rounded-full w-3 h-3" :class="steps.finish.active ? 'bg-orange-500' : 'bg-slate-400 dark:bg-slate-800'"></div>
            </div>
            <Button fluid @click="nextStep()">
                {{ buttonLabel }}
            </Button>
        </div>
    </div>
</template>
<script setup lang="ts">
import { Button } from 'primevue'
import AuthLayout from '~/Layouts/AuthLayout.vue'
import { useAuth, useWebAuthn } from '~/Composables/auth'
import { computed, ref } from 'vue'
import { useVaults } from '~/Composables/vaults'
import WelcomeStep from '~/Components/Onboard/WelcomeStep.vue'
import PasswordStep from '~/Components/Onboard/PasswordStep.vue'
import CreateVaultStep from '~/Components/Onboard/CreateVaultStep.vue'
import BiometricStep from '~/Components/Onboard/BiometricStep.vue'
import FinishStep from '~/Components/Onboard/FinishStep.vue'
import axios from 'axios'

const { user } = useAuth()
const { vaults, loadVaults, loadingVaults } = useVaults()

const { hasAuthnSetup } = useWebAuthn()

const passwordStep = ref();
const vaultStep = ref();

const activeStep = ref('welcome')

const hasOnboarded = computed(() => !!user.value?.onboard)
const hasVaults = computed(() => !!vaults.value?.length)

const buttonLabel = computed(() => {
    switch (activeStep.value) {
        case 'welcome':
            return 'Get Started!'
        case 'finish':
            return 'Let\'s get started!'
        case 'biometrics':
            return hasAuthnSetup.value ? 'Next' : 'Skip'
        default:
            return 'Next'
    }
})

const steps = computed(() => ({
    welcome: { enabled: true, active: true, },
    password: { enabled: !hasOnboarded.value, active: false, },
    vaults: { enabled: !hasVaults.value, active: false, },
    biometrics: { enabled: true, active: false },
    finish: { enabled: true, active: false },
}))

const nextStep = async (force = false) => {
    if (activeStep.value === 'finish') {
        await axios.post('/onboard')
        // force relocation
        window.location.href = '/';
        return
    }

    if (activeStep.value === 'password' && force === false) {
        passwordStep.value.submit();
        return;
    }

    if (activeStep.value === 'vaults' && force === false) {
        vaultStep.value.submit();
        return;
    }

    let getNext = false;
    for (const step in steps.value) {
        if (getNext && steps.value[step].enabled) {
            activeStep.value = step
            steps.value[activeStep.value].active = true
            return
        }
        if (step === activeStep.value) {
            getNext = true
        }
    }

    console.error('No step to proceed')
}

loadVaults()

defineOptions({ layout: AuthLayout })
</script>
