<script setup lang="ts">
import dayjs from 'dayjs'

const model = defineModel<string>()

interface Props {
  class?: object
}

const props = defineProps<Props>()

const onDateChange = (value: Date | Array<Date> | Array<Date | null> | undefined | null) => {
  const d = value instanceof Date ? dayjs(value) : null
  model.value = d ? d.format('YYYY-MM-DD') : undefined
}
</script>

<template>
  <DatePicker
    id="start" required date-format="dd/mm/yy"
    :manual-input="false" fluid
    :input-class="props.class"
    @value-change="onDateChange"
  />
</template>

<style scoped lang="scss">

</style>
