<script setup lang="ts">
import { ref } from 'vue'
import type { DataTablePageEvent } from 'primevue/datatable'
import ListMiscConfigurations from '~/useCases/CoreConfiguration/MiscConfiguration/ListMiscConfigurations'
import DeleteMiscConfiguration from '~/useCases/CoreConfiguration/MiscConfiguration/DeleteMiscConfiguration'
import type { MiscConfigurationJsonldReadMiscConfiguration } from '~/services/client'
import MiscConfigurationFormDialog from '~/components/CoreConfiguration/MiscConfiguration/Dialog/MiscConfigurationFormDialog.vue'
import MiscConfigurationUpdateFormDialog from '~/components/CoreConfiguration/MiscConfiguration/Dialog/MiscConfigurationUpdateFormDialog.vue'

const confirm = useConfirm()
const dt = ref()
const selectedMiscConfigurations = ref()
const cfd = useTemplateRef<typeof MiscConfigurationFormDialog>('cfd')
const cufd = useTemplateRef<typeof MiscConfigurationUpdateFormDialog>('cufd')

const itemsPerPage = ref(30)
const currentPage = ref(0)
const onPage = (event: DataTablePageEvent) => {
  currentPage.value = event.page
  itemsPerPage.value = event.rows
}
const miscConfigQuery = computed(() => ({ itemsPerPage: itemsPerPage.value, page: currentPage.value + 1 }))

const { isLoading, data } = ListMiscConfigurations(miscConfigQuery)
const { mutate } = DeleteMiscConfiguration()

const openNew = () => {
  if (!cfd.value) {
    return
  }

  cfd.value.open()
}

const editMiscConfiguration = (miscConfiguration: MiscConfigurationJsonldReadMiscConfiguration) => {
  if (!cufd.value) {
    return
  }

  cufd.value.open(miscConfiguration)
}

const confirmDeleteMiscConfiguration = (miscConfiguration: MiscConfigurationJsonldReadMiscConfiguration) => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer cette configuration ?',
    header: 'Suppression d\'une configuration',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      if (!miscConfiguration.id) {
        return
      }

      mutate(miscConfiguration.id)
    },
  })
}

const confirmDeleteSelected = () => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer ces configurations ?',
    header: 'Suppression de plusieurs configurations',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      for (const miscConfiguration of selectedMiscConfigurations.value) {
        if (!miscConfiguration.id) {
          continue
        }

        mutate(miscConfiguration.id)
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
            label="Ajouter une configuration"
            icon="pi pi-plus"
            severity="secondary"
            class="mr-2"
            @click="openNew"
          />
          <Button
            label="Supprimer la sélection"
            icon="pi pi-trash"
            severity="danger"
            :disabled="!selectedMiscConfigurations || !selectedMiscConfigurations.length"
            @click="confirmDeleteSelected"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedMiscConfigurations"
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
        current-page-report-template="Liste {first} à {last} sur {totalRecords} configurations"
        @page="onPage"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">
              Gestion des Configurations
            </h4>
          </div>
        </template>

        <Column
          selection-mode="multiple"
          style="width: 3rem"
          :exportable="false"
        />
        <Column
          field="key"
          header="Clé"
          style="min-width: 12rem"
        />
        <Column
          field="value"
          header="Valeur"
          style="min-width: 12rem"
        />
        <Column
          field="description"
          header="Description"
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
              @click="editMiscConfiguration(slotProps.data)"
            />
            <Button
              icon="pi pi-trash"
              outlined
              rounded
              severity="danger"
              @click="confirmDeleteMiscConfiguration(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <MiscConfigurationFormDialog ref="cfd" />
    <MiscConfigurationUpdateFormDialog ref="cufd" />
  </div>
</template>

<style scoped lang="scss">

</style>
