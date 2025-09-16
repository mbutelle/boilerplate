<script setup lang="ts">
import CreateStaticContent from '~/useCases/CoreConfiguration/CMS/StaticContent/CreateStaticContent'

const { isPending, isError, mutate } = CreateStaticContent()

const visible = ref(false)

const staticContent = ref({
  code: '',
  title: '',
  content: '',
})

const open = () => {
  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  staticContent.value = {
    code: '',
    title: '',
    content: '',
  }
}

const saveStaticContent = async () => {
  if (!staticContent.value.code?.trim()) {
    return
  }

  mutate(staticContent.value, {
    onSuccess() {
      hideDialog()
    },
  })
}

defineExpose({
  open,
})
</script>

<template>
  <Dialog
    v-model:visible="visible"
    :style="{ width: '750px' }"
    header="Ajouter un contenu statique"
    :modal="true"
  >
    <div class="flex flex-col gap-6">
      <div>
        <label
          for="code"
          class="block font-bold mb-3"
        >Code</label>
        <InputText
          id="code"
          v-model.trim="staticContent.code"
          required
          autofocus
          :invalid="isError || !staticContent.code?.trim()"
          fluid
        />
        <small
          v-if="isError || !staticContent.code?.trim()"
          class="text-red-500"
        >Le code est requis.</small>
      </div>
      <div>
        <label
          for="title"
          class="block font-bold mb-3"
        >Titre</label>
        <InputText
          id="title"
          v-model.trim="staticContent.title"
          fluid
        />
      </div>
      <div>
        <label
          for="content"
          class="block font-bold mb-3"
        >Contenu (HTML)</label>
        <Editor
          id="content"
          v-model="staticContent.content"
          auto-resize
          rows="10"
          fluid
          editor-style="height: 320px"
        />
      </div>
    </div>

    <template #footer>
      <Button
        label="Annuler"
        icon="pi pi-times"
        text
        @click="hideDialog"
      />
      <Button
        :loading="isPending"
        label="Enregistrer"
        icon="pi pi-check"
        @click="saveStaticContent"
      />
    </template>
  </Dialog>
</template>

<style scoped lang="scss">
</style>
