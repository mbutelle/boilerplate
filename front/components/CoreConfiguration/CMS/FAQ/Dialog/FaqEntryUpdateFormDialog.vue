<script setup lang="ts">
import type { FaqEntryJsonld, FaqEntry } from '~/services/client'
import UpdateFaqEntry from '~/useCases/CoreConfiguration/CMS/FAQ/UpdateFaqEntry'

const { isPending, mutate } = UpdateFaqEntry()

const visible = ref(false)

const faqEntryToUpdate = ref<FaqEntryJsonld>()
const faqEntry = ref<FaqEntry>({})

const open = (current: FaqEntryJsonld) => {
  faqEntryToUpdate.value = current
  faqEntry.value = {
    question: current.question,
    answer: current.answer,
  }

  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  faqEntry.value = {}
}

const saveFaqEntry = async () => {
  if (!faqEntry.value.question?.trim() || !faqEntry.value.answer?.trim() || !faqEntryToUpdate.value?.id) {
    return
  }

  mutate({
    id: faqEntryToUpdate.value.id,
    faqEntry: faqEntry.value,
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
    :header="'Mise à jour de la FAQ'"
    :modal="true"
  >
    <div class="flex flex-col gap-6">
      <div>
        <label
          for="question"
          class="block font-bold mb-3"
        >Question</label>
        <Textarea
          id="question"
          v-model.trim="faqEntry.question"
          required
          autofocus
          fluid
          rows="3"
        />
      </div>
      <div>
        <label
          for="answer"
          class="block font-bold mb-3"
        >Réponse</label>
        <Textarea
          id="answer"
          v-model.trim="faqEntry.answer"
          required
          fluid
          rows="5"
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
        @click="saveFaqEntry"
      />
    </template>
  </Dialog>
</template>

<style scoped lang="scss">

</style>
