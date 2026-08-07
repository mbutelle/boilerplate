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
    label: 'Catalogue',
    items: [
      { label: 'Produits', icon: 'pi pi-fw pi-box', to: '/catalog/products' },
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
