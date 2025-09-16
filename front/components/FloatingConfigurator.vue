<script setup>
import AppConfigurator from '~/layouts/AppConfigurator.vue'

const { toggleDarkMode, isDarkTheme } = UseLayout()
</script>

<template>
  <div class="fixed flex gap-4 top-8 right-8">
    <Button
      type="button"
      rounded
      :icon="isDarkTheme ? 'pi pi-moon' : 'pi pi-sun'"
      severity="secondary"
      @click="toggleDarkMode"
    />
    <div class="relative">
      <Button
        v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
        icon="pi pi-palette"
        type="button"
        rounded
      />
      <AppConfigurator />
    </div>
  </div>
</template>
