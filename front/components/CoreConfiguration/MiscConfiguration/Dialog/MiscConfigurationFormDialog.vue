<script setup lang="ts">
import CreateMiscConfiguration from '~/useCases/CoreConfiguration/MiscConfiguration/CreateMiscConfiguration'

const { isPending, isError, mutate } = CreateMiscConfiguration()

const visible = ref(false)

const miscConfiguration = ref({
  key: '',
  value: '',
  description: null,
})

const open = () => {
  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  miscConfiguration.value = {
    key: '',
    value: '',
    description: null,
  }
}

const saveMiscConfiguration = async () => {
  if (!miscConfiguration.value.key?.trim() || !miscConfiguration.value.value?.trim()) {
    return
  }

  mutate(miscConfiguration.value, {
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
    header="Ajouter une nouvelle configuration"
    :modal="true"
  >
    <div class="flex flex-col gap-6">
      <div>
        <label
          for="key"
          class="block font-bold mb-3"
        >Clé</label>
        <InputText
          id="key"
          v-model.trim="miscConfiguration.key"
          required
          autofocus
          :invalid="isError"
          fluid
        />
        <small
          v-if="isError"
          class="text-red-500"
        >La clé est requise.</small>
      </div>
      <div>
        <label
          for="value"
          class="block font-bold mb-3"
        >Valeur</label>
        <InputText
          id="value"
          v-model.trim="miscConfiguration.value"
          required
          :invalid="isError"
          fluid
        />
        <small
          v-if="isError"
          class="text-red-500"
        >La valeur est requise.</small>
      </div>
      <div>
        <label
          for="description"
          class="block font-bold mb-3"
        >Description</label>
        <Textarea
          id="description"
          v-model.trim="miscConfiguration.description"
          :rows="4"
          fluid
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
        @click="saveMiscConfiguration"
      />
    </template>
  </Dialog>
</template>

<style scoped lang="scss">

</style>
