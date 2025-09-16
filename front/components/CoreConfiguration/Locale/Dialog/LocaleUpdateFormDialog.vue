<script setup lang="ts">
import type { LocaleJsonld, Locale } from '~/services/client'
import UpdateLocale from '~/useCases/CoreConfiguration/Locale/UpdateLocale'

const { isPending, mutate } = UpdateLocale()

const visible = ref(false)

const localeToUpdate = ref<LocaleJsonld>()
const locale = ref<Locale>({})

const open = (current: LocaleJsonld) => {
  localeToUpdate.value = current
  locale.value = {
    code: current.code,
    name: current.name,
    autoTranslate: current.autoTranslate,
  }

  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  locale.value = {}
}

const saveLocale = async () => {
  if (!locale.value.code?.trim() || !locale.value.name?.trim() || !localeToUpdate.value?.id) {
    return
  }

  mutate({
    id: localeToUpdate.value.id,
    locale: locale.value,
  }, {
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
    :header="'Mise à jour de ' + localeToUpdate?.name"
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
          fluid
          maxlength="5"
        />
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
          fluid
        />
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
