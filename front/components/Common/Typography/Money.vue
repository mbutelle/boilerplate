<script setup lang="ts">
interface Props {
  amount: number
  currency: string
  locale?: string
}

const props = defineProps<Props>()

function formatCurrency(value: number) {
  return value.toLocaleString(props.locale, { style: 'currency', currency: props.currency })
}
</script>

<template>
  <span>
    {{ formatCurrency(props.amount) }}
  </span>
</template>

<style scoped lang="scss">

</style>
