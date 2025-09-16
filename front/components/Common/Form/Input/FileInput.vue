<script setup lang="ts">
import type { FileUploadSelectEvent } from 'primevue/fileupload'

const model = defineModel<Blob>()

const onFileSelect = (event: FileUploadSelectEvent) => {
  model.value = event.files[0]
}
</script>

<template>
  <FileUpload
    mode="basic"
    custom-upload
    @select="onFileSelect"
  />
</template>

<style scoped lang="scss">

</style>
