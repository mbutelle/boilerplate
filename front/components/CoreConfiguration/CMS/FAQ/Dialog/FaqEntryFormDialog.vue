<script setup lang="ts">
import CreateFaqEntry from '~/useCases/CoreConfiguration/CMS/FAQ/CreateFaqEntry'

const { isPending, isError, mutate } = CreateFaqEntry()

const visible = ref(false)

const faqEntry = ref({
  question: '',
  answer: '',
})

const open = () => {
  visible.value = true
}

const hideDialog = () => {
  visible.value = false
  faqEntry.value = {
    question: '',
    answer: '',
  }
}

const saveFaqEntry = async () => {
  if (!faqEntry.value.question?.trim() || !faqEntry.value.answer?.trim()) {
    return
  }

  mutate(faqEntry.value, {
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
    header="Ajouter une nouvelle FAQ"
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
          :invalid="isError"
          fluid
          rows="3"
        />
        <small
          v-if="isError"
          class="text-red-500"
        >La question est requise.</small>
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
          :invalid="isError"
          fluid
          rows="5"
        />
        <small
          v-if="isError"
          class="text-red-500"
        >La réponse est requise.</small>
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
