<script setup lang="ts">
import type { MiscConfigurationJsonldReadMiscConfiguration, MiscConfigurationWriteMiscConfiguration } from '~/services/client'
import UpdateMiscConfiguration from '~/useCases/CoreConfiguration/MiscConfiguration/UpdateMiscConfiguration'

const { isPending, mutate } = UpdateMiscConfiguration()

const visible = ref(false)

const miscConfigurationToUpdate = ref<MiscConfigurationJsonldReadMiscConfiguration>()
const miscConfiguration = ref<MiscConfigurationWriteMiscConfiguration>({})

const open = (current: MiscConfigurationJsonldReadMiscConfiguration) => {
  miscConfigurationToUpdate.value = current
  miscConfiguration.value = {
    key: current.key,
    value: current.value,
    description: current.description,
  }

  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  miscConfiguration.value = {}
}

const saveMiscConfiguration = async () => {
  if (!miscConfiguration.value.key?.trim() || !miscConfiguration.value.value?.trim() || !miscConfigurationToUpdate.value?.id) {
    return
  }

  mutate({
    id: miscConfigurationToUpdate.value.id,
    miscConfiguration: miscConfiguration.value,
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
    :header="'Mise à jour de ' + miscConfigurationToUpdate?.key"
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
          fluid
        />
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
          fluid
        />
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
