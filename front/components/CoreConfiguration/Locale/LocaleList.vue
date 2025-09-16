<script setup lang="ts">
import { ref } from 'vue'
import type { DataTablePageEvent } from 'primevue/datatable'
import ListLocales from '~/useCases/CoreConfiguration/Locale/ListLocales'
import DeleteLocale from '~/useCases/CoreConfiguration/Locale/DeleteLocale'
import type { LocaleJsonld } from '~/services/client'
import LocaleFormDialog from '~/components/CoreConfiguration/Locale/Dialog/LocaleFormDialog.vue'
import LocaleUpdateFormDialog from '~/components/CoreConfiguration/Locale/Dialog/LocaleUpdateFormDialog.vue'

const confirm = useConfirm()
const dt = ref()
const selectedLocales = ref()
const lfd = useTemplateRef<typeof LocaleFormDialog>('lfd')
const lufd = useTemplateRef<typeof LocaleUpdateFormDialog>('lufd')

const itemsPerPage = ref(30)
const currentPage = ref(0)
const onPage = (event: DataTablePageEvent) => {
  currentPage.value = event.page
  itemsPerPage.value = event.rows
}
const localeQuery = computed(() => ({ itemsPerPage: itemsPerPage.value, page: currentPage.value + 1 }))

const { isLoading, data } = ListLocales(localeQuery)
const { mutate } = DeleteLocale()

const openNew = () => {
  if (!lfd.value) {
    return
  }

  lfd.value.open()
}

const editLocale = (locale: LocaleJsonld) => {
  if (!lufd.value) {
    return
  }

  lufd.value.open(locale)
}

const confirmDeleteLocale = (locale: LocaleJsonld) => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer cette locale ?',
    header: 'Suppression d\'une locale',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      if (!locale.id) {
        return
      }

      mutate(locale.id)
    },
  })
}

const confirmDeleteSelected = () => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer ces locales ?',
    header: 'Suppression de plusieurs locales',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      for (const locale of selectedLocales.value) {
        if (!locale.id) {
          continue
        }

        mutate(locale.id)
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
            label="Ajouter une locale"
            icon="pi pi-plus"
            severity="secondary"
            class="mr-2"
            @click="openNew"
          />
          <Button
            label="Supprimer la sélection"
            icon="pi pi-trash"
            severity="danger"
            :disabled="!selectedLocales || !selectedLocales.length"
            @click="confirmDeleteSelected"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedLocales"
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
        current-page-report-template="Liste {first} à {last} sur {totalRecords} locales"
        @page="onPage"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">
              Gestion des Locales
            </h4>
          </div>
        </template>

        <Column
          selection-mode="multiple"
          style="width: 3rem"
          :exportable="false"
        />
        <Column
          field="code"
          header="Code"
          style="min-width: 8rem"
        />
        <Column
          field="name"
          header="Nom"
          style="min-width: 12rem"
        />
        <Column
          field="autoTranslate"
          header="Traduction automatique"
          style="min-width: 10rem"
        >
          <template #body="slotProps">
            <i
              :class="slotProps.data.autoTranslate ? 'pi pi-check text-green-500' : 'pi pi-times text-red-500'"
            />
          </template>
        </Column>
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
              @click="editLocale(slotProps.data)"
            />
            <Button
              icon="pi pi-trash"
              outlined
              rounded
              severity="danger"
              @click="confirmDeleteLocale(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <LocaleFormDialog ref="lfd" />
    <LocaleUpdateFormDialog ref="lufd" />
  </div>
</template>

<style scoped lang="scss">

</style>
