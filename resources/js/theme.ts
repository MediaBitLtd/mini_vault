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
        colorScheme: {
            light: {
                content: {
                    background: '{surface.100}',
                },
            },
            dark: {
                primary: {
                    contrastColor: '{surface.100}',
                },
                surface: {
                    50: '{zinc.50}',
                    100: '{zinc.100}',
                    200: '{zinc.200}',
                    300: '{zinc.300}',
                    400: '{zinc.400}',
                    500: '{zinc.500}',
                    600: '{zinc.600}',
                    700: '{zinc.700}',
                    800: '{zinc.800}',
                    900: '{zinc.900}',
                    950: '{zinc.950}',
                },
                content: {
                    background: '{surface.800}',
                },
            },
        },
    },
    components: {
        floatlabel: {
            colorScheme: {
                light: {
                    focus: {
                        color: '{neutral.700}',
                    },
                },
            },
        },
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
