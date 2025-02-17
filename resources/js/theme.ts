import { definePreset } from '@primevue/themes'
import Aura from '@primevue/themes/aura'

export default definePreset(Aura, {
    semantic: {
        primary: {
            50: "#fff6f3",
            100: "#ffd2c3",
            200: "#ffaf94",
            300: "#ff8b64",
            400: "#ff6835",
            500: "#ff4405",
            600: "#d93a04",
            700: "#b33004",
            800: "#8c2503",
            900: "#661b02",
            950: "#401101",
        },
        surface: {
            50: '{neutral.50}',
            100: '{neutral.100}',
            200: '{neutral.200}',
            300: '{neutral.300}',
            400: '{neutral.400}',
            500: '{neutral.500}',
            600: '{neutral.600}',
            700: '{neutral.700}',
            800: '{neutral.800}',
            900: '{neutral.900}',
            950: '{neutral.950}',
        },
        colorScheme: {
            dark: {
                primary: {
                    contrastColor: '{surface.100}',
                },
            },
        },
    },
    components: {
        inputtext: {
            colorScheme: {
                light: {
                    background: '{surface.100}',
                    focus: {
                        borderColor: '{neutral.700}',
                    },
                },
                dark: {
                    background: 'transparent',
                    focus: {
                        borderColor: '{primary.400}',
                    },
                }
            },
        },
        button: {
            colorScheme: {
                light: {},
                dark: {
                    root: {
                        danger: {
                            // background: '#ff0000',
                        }
                    },
                }
            }
        }
    }
})
