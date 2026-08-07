import Lara from '@primeuix/themes/lara'

export default defineNuxtConfig({
  modules: [
    '@primevue/nuxt-module',
    '@nuxt/test-utils/module',
    '@nuxt/eslint',
    '@nuxtjs/tailwindcss',
  ],
  ssr: false,
  devtools: { enabled: true },
  css: [
    '@fortawesome/fontawesome-free/css/all.css',
    'primeicons/primeicons.css',
    '~/assets/css/styles.scss',
  ],
  runtimeConfig: {
    public: {
      API_URL: process.env.API_URL,
    },
  },
  compatibilityDate: '2025-08-01',
  eslint: {
    config: {
      stylistic: true,
    },
  },
  primevue: {
    options: {
      theme: {
        preset: Lara,
        options: {
          darkModeSelector: '.app-dark',

        },
      },
    },
  },
})
