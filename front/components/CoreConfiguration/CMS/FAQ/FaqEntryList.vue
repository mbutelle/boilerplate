<script setup lang="ts">
import { ref } from 'vue'
import type { DataTablePageEvent } from 'primevue/datatable'
import ListFaqEntries from '~/useCases/CoreConfiguration/CMS/FAQ/ListFaqEntries'
import DeleteFaqEntry from '~/useCases/CoreConfiguration/CMS/FAQ/DeleteFaqEntry'
import type { FaqEntryJsonld } from '~/services/client'
import FaqEntryFormDialog from '~/components/CoreConfiguration/CMS/FAQ/Dialog/FaqEntryFormDialog.vue'
import FaqEntryUpdateFormDialog from '~/components/CoreConfiguration/CMS/FAQ/Dialog/FaqEntryUpdateFormDialog.vue'

const confirm = useConfirm()
const dt = ref()
const selectedFaqEntries = ref()
const fefd = useTemplateRef<typeof FaqEntryFormDialog>('fefd')
const feufd = useTemplateRef<typeof FaqEntryUpdateFormDialog>('feufd')

const itemsPerPage = ref(30)
const currentPage = ref(0)
const onPage = (event: DataTablePageEvent) => {
  currentPage.value = event.page
  itemsPerPage.value = event.rows
}
const faqQuery = computed(() => ({ itemsPerPage: itemsPerPage.value, page: currentPage.value + 1 }))

const { isLoading, data } = ListFaqEntries(faqQuery)
const { mutate } = DeleteFaqEntry()

const openNew = () => {
  if (!fefd.value) {
    return
  }

  fefd.value.open()
}

const editFaqEntry = (faqEntry: FaqEntryJsonld) => {
  if (!feufd.value) {
    return
  }

  feufd.value.open(faqEntry)
}

const confirmDeleteFaqEntry = (faqEntry: FaqEntryJsonld) => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer cette FAQ ?',
    header: 'Suppression d\'une FAQ',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      if (!faqEntry.id) {
        return
      }

      mutate(faqEntry.id)
    },
  })
}

const confirmDeleteSelected = () => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer ces FAQs ?',
    header: 'Suppression de plusieurs FAQs',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      for (const faqEntry of selectedFaqEntries.value) {
        if (!faqEntry.id) {
          continue
        }

        mutate(faqEntry.id)
      }
    },
  })
}
</script>

<template>
  <div>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button
            label="Ajouter une FAQ"
            icon="pi pi-plus"
            severity="secondary"
            class="mr-2"
            @click="openNew"
          />
          <Button
            label="Supprimer la sélection"
            icon="pi pi-trash"
            severity="danger"
            :disabled="!selectedFaqEntries || !selectedFaqEntries.length"
            @click="confirmDeleteSelected"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedFaqEntries"
        data-key="id"
        :paginator="true"
        edit-mode="row"
        :value="data?.member ?? []"
        :total-records="data?.totalItems ?? 0"
        :loading="isLoading"
        :lazy="true"
        :rows="itemsPerPage"
        :rows-per-page-options="[10, 20, 30, 50]"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Liste {first} à {last} sur {totalRecords} FAQs"
        @page="onPage"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">
              Gestion des FAQs
            </h4>
          </div>
        </template>

        <Column
          selection-mode="multiple"
          style="width: 3rem"
          :exportable="false"
        />
        <Column
          field="question"
          header="Question"
          style="min-width: 12rem"
        />
        <Column
          field="answer"
          header="Réponse"
          style="min-width: 12rem"
        />
        <Column
          :exportable="false"
          style="min-width: 12rem"
        >
          <template #body="slotProps">
            <Button
              icon="pi pi-pencil"
              outlined
              rounded
              class="mr-2"
              @click="editFaqEntry(slotProps.data)"
            />
            <Button
              icon="pi pi-trash"
              outlined
              rounded
              severity="danger"
              @click="confirmDeleteFaqEntry(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <FaqEntryFormDialog ref="fefd" />
    <FaqEntryUpdateFormDialog ref="feufd" />
  </div>
</template>

<style scoped lang="scss">

</style>
