<script setup>
import { ref } from 'vue'

import AppMenuItem from './AppMenuItem.vue'

const model = ref([
  {
    label: 'Accueil',
    items: [{
      label: 'Accueil',
      icon: 'fa-solid fa-cart-shopping',
      to: '/',
    }],
  },
  {
    label: 'Configuration',
    items: [
      { label: 'Locales', icon: 'pi pi-fw pi-globe', to: '/core-configuration/locales' },
      { label: 'FAQ', icon: 'pi pi-fw pi-question-circle', to: '/core-configuration/faq' },
      { label: 'Contenus statiques', icon: 'pi pi-fw pi-file', to: '/core-configuration/static-contents' },
      { label: 'Utilisateurs', icon: 'pi pi-fw pi-users', to: '/core-configuration/admin' },
      { label: 'Configurations', icon: 'pi pi-fw pi-cog', to: '/core-configuration/misc-configurations' },
    ],
  },
])
</script>

<template>
  <ul class="layout-menu">
    <template
      v-for="(item, i) in model"
      :key="item"
    >
      <AppMenuItem
        v-if="!item.separator"
        :item="item"
        :index="i"
      />
      <li
        v-if="item.separator"
        class="menu-separator"
      />
    </template>
  </ul>
</template>

<style lang="scss" scoped></style>
