import Aura from '@primevue/themes/aura';

export default defineNuxtConfig({
    compatibilityDate: '2024-04-03',
    devtools: {enabled: true},
    modules: [
        '@primevue/nuxt-module',
        '@nuxt/test-utils/module'
    ],
    primevue: {
        options: {
            theme: {
                preset: Aura
            }
        }
    }
})
