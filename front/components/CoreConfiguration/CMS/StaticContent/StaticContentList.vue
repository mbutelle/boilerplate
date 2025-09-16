<script setup lang="ts">
import { ref } from 'vue'
import type { DataTablePageEvent } from 'primevue/datatable'
import ListStaticContents from '~/useCases/CoreConfiguration/CMS/StaticContent/ListStaticContents'
import DeleteStaticContent from '~/useCases/CoreConfiguration/CMS/StaticContent/DeleteStaticContent'
import type { StaticContentJsonld } from '~/services/client'
import StaticContentFormDialog from '~/components/CoreConfiguration/CMS/StaticContent/Dialog/StaticContentFormDialog.vue'
import StaticContentUpdateFormDialog from '~/components/CoreConfiguration/CMS/StaticContent/Dialog/StaticContentUpdateFormDialog.vue'

const confirm = useConfirm()
const dt = ref()
const selectedStaticContents = ref()
const scfd = useTemplateRef<typeof StaticContentFormDialog>('scfd')
const scufd = useTemplateRef<typeof StaticContentUpdateFormDialog>('scufd')

const itemsPerPage = ref(30)
const currentPage = ref(0)
const onPage = (event: DataTablePageEvent) => {
  currentPage.value = event.page
  itemsPerPage.value = event.rows
}
const staticContentQuery = computed(() => ({ itemsPerPage: itemsPerPage.value, page: currentPage.value + 1 }))

const { isLoading, data } = ListStaticContents(staticContentQuery)
const { mutate } = DeleteStaticContent()

const openNew = () => {
  if (!scfd.value) {
    return
  }

  scfd.value.open()
}

const editStaticContent = (staticContent: StaticContentJsonld) => {
  if (!scufd.value) {
    return
  }

  scufd.value.open(staticContent)
}

const confirmDeleteStaticContent = (staticContent: StaticContentJsonld) => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer ce contenu statique ?',
    header: 'Suppression d\'un contenu statique',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      if (!staticContent.id) {
        return
      }

      mutate(staticContent.id)
    },
  })
}

const confirmDeleteSelected = () => {
  confirm.require({
    message: 'Voulez-vous vraiment supprimer ces contenus statiques ?',
    header: 'Suppression de plusieurs contenus statiques',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'oui',
    rejectLabel: 'non',
    acceptClass: 'p-button-danger',
    accept: () => {
      for (const staticContent of selectedStaticContents.value) {
        if (!staticContent.id) {
          continue
        }

        mutate(staticContent.id)
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
            label="Ajouter un contenu statique"
            icon="pi pi-plus"
            severity="secondary"
            class="mr-2"
            @click="openNew"
          />
          <Button
            label="Supprimer la sélection"
            icon="pi pi-trash"
            severity="danger"
            :disabled="!selectedStaticContents || !selectedStaticContents.length"
            @click="confirmDeleteSelected"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedStaticContents"
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
        current-page-report-template="Liste {first} à {last} sur {totalRecords} contenus statiques"
        @page="onPage"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">
              Gestion des contenus statiques
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
          style="min-width: 10rem"
        />
        <Column
          field="title"
          header="Titre"
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
              @click="editStaticContent(slotProps.data)"
            />
            <Button
              icon="pi pi-trash"
              outlined
              rounded
              severity="danger"
              @click="confirmDeleteStaticContent(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <StaticContentFormDialog ref="scfd" />
    <StaticContentUpdateFormDialog ref="scufd" />
  </div>
</template>

<style scoped lang="scss">
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.max-w-80 { max-width: 20rem; }
</style>
