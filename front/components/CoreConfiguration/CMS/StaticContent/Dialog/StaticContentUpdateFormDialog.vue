<script setup lang="ts">
import type { StaticContentJsonld, StaticContent } from '~/services/client'
import UpdateStaticContent from '~/useCases/CoreConfiguration/CMS/StaticContent/UpdateStaticContent'

const { isPending, mutate } = UpdateStaticContent()

const visible = ref(false)

const staticContentToUpdate = ref<StaticContentJsonld>()
const staticContent = ref<StaticContent>({})

const open = (current: StaticContentJsonld) => {
  staticContentToUpdate.value = current
  staticContent.value = {
    code: current.code,
    title: current.title,
    content: current.content,
  }

  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  staticContent.value = {}
}

const saveStaticContent = async () => {
  if (!staticContentToUpdate.value?.id) {
    return
  }

  mutate({
    id: staticContentToUpdate.value.id,
    staticContent: staticContent.value,
  }, {
    onSuccess() {
      hideDialog()
    },
  })
}

// code should not change after creation to keep uniqueness and references stable
const codeReadonly = true

defineExpose({
  open,
})
</script>

<template>
  <Dialog
    v-model:visible="visible"
    :style="{ width: '750px' }"
    :header="'Mise à jour du contenu statique'"
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
          :readonly="codeReadonly"
          fluid
        />
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
