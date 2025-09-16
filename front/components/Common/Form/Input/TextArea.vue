<script setup lang="ts">
import Textarea from 'primevue/textarea'

const model = defineModel<string>()

const textarea = useTemplateRef<typeof Textarea>('textarea')
</script>

<template>
  <Textarea ref="textarea" v-model="model" />
</template>

<style scoped lang="scss">

</style>
