<script setup lang="ts">
import CreateLocale from '~/useCases/CoreConfiguration/Locale/CreateLocale'

const { isPending, isError, mutate } = CreateLocale()

const visible = ref(false)

const locale = ref({
  code: '',
  name: '',
  autoTranslate: false,
})

const open = () => {
  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  locale.value = {
    code: '',
    name: '',
    autoTranslate: false,
  }
}

const saveLocale = async () => {
  if (!locale.value.code?.trim() || !locale.value.name?.trim()) {
    return
  }

  mutate(locale.value, {
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
    :style="{ width: '450px' }"
    header="Ajouter une nouvelle locale"
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
          v-model.trim="locale.code"
          required
          autofocus
          :invalid="isError"
          fluid
          maxlength="5"
        />
        <small
          v-if="isError"
          class="text-red-500"
        >Le code est requis (5 caractères max).</small>
      </div>
      <div>
        <label
          for="name"
          class="block font-bold mb-3"
        >Nom</label>
        <InputText
          id="name"
          v-model.trim="locale.name"
          required
          :invalid="isError"
          fluid
        />
        <small
          v-if="isError"
          class="text-red-500"
        >Le nom est requis.</small>
      </div>
      <div>
        <label
          for="autoTranslate"
          class="block font-bold mb-3"
        >Traduction automatique</label>
        <Checkbox
          id="autoTranslate"
          v-model="locale.autoTranslate"
          :binary="true"
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
        @click="saveLocale"
      />
    </template>
  </Dialog>
</template>

<style scoped lang="scss">

</style>
