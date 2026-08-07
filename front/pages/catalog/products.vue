<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import ListProducts from '~/useCases/Catalog/Products/ListProducts'
import PublishProduct from '~/useCases/Catalog/Products/PublishProduct'
import type { ListProductsParams } from '@bp/api-client/api/model'

definePageMeta({ layout: 'default' })

const search = ref('')
const status = ref<ListProductsParams['status']>(undefined)
const page = ref(1)

// Les paramètres sont une ref : vue-query relance la requête quand ils changent,
// et la pagination reste côté serveur. Filtrer un tableau déjà chargé dans le
// navigateur ne tient que sur un jeu de démonstration.
const params = computed<ListProductsParams>(() => ({
  'page': page.value,
  'itemsPerPage': 20,
  'query': search.value || undefined,
  'status': status.value,
  'sort[name]': 'ASC',
}))

const { products, totalItems, isPending } = ListProducts(params)
const { publishProduct } = PublishProduct()

watch([search, status], () => {
  page.value = 1
})

const statusOptions = [
  { label: 'Tous', value: undefined },
  { label: 'Brouillon', value: 'draft' },
  { label: 'Publié', value: 'published' },
  { label: 'Archivé', value: 'archived' },
]

// formatPrice est auto-importé depuis utils/ : il est testé sans monter Nuxt.
</script>

<template>
  <div class="card">
    <h1 class="text-xl font-semibold mb-4">
      Produits
    </h1>

    <div class="flex gap-3 mb-4">
      <InputText
        v-model="search"
        placeholder="Rechercher un nom ou une référence"
      />
      <Select
        v-model="status"
        :options="statusOptions"
        option-label="label"
        option-value="value"
        placeholder="Statut"
      />
    </div>

    <DataTable
      :value="products"
      :loading="isPending"
      lazy
      paginator
      :rows="20"
      :total-records="totalItems"
      @page="page = $event.page + 1"
    >
      <Column field="reference" header="Référence" />
      <Column field="name" header="Nom" />
      <Column header="Prix">
        <template #body="{ data }">
          {{ formatPrice(data.priceAmount, data.priceCurrency) }}
        </template>
      </Column>
      <Column field="status" header="Statut" />
      <Column header="">
        <template #body="{ data }">
          <Button
            v-if="'published' !== data.status"
            label="Publier"
            size="small"
            @click="publishProduct(data.id)"
          />
        </template>
      </Column>
    </DataTable>
  </div>
</template>
