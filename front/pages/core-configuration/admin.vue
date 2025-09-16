<script setup lang="ts">
import { ref } from 'vue'
import useListAdmins from '~/useCases/CoreConfiguration/Admins/ListAdmins'
import useCreateAdmin from '~/useCases/CoreConfiguration/Admins/CreateAdmin'
import useDeleteAdmin from '~/useCases/CoreConfiguration/Admins/DeleteAdmin'
// PrimeVue ConfirmDialog service and component
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import type { UserJsonld } from '~/services/client'

const email = ref('')
const message = ref<string | null>(null)
const error = ref<string | null>(null)
const deletingId = ref<string | null>(null)

// Use cases
const { data: admins, isLoading } = useListAdmins()
const { mutateAsync: createAdmin, isPending } = useCreateAdmin()
const { mutateAsync: deleteAdmin } = useDeleteAdmin()

// PrimeVue confirm service
const confirm = useConfirm()

function onRemove(user: UserJsonld) {
  if (!user.id) return

  confirm.require({
    message: 'Confirmer la suppression de cet utilisateur ?',
    header: 'Confirmation',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Supprimer',
    rejectLabel: 'Annuler',
    acceptClass: 'p-button-danger',
    accept: async () => {
      message.value = null
      error.value = null
      try {
        if (!user.id) {
          return
        }

        await deleteAdmin(user.id.toString())
        message.value = 'Administrateur supprimé.'
      }
      catch (e: unknown) {
        console.error(e)
        error.value = 'Erreur lors de la suppression'
      }
      finally {
        deletingId.value = null
      }
    },
    reject: () => {
      /* no-op */
    },
  })
}

const submit = async () => {
  message.value = null
  error.value = null
  if (!email.value) {
    error.value = 'Email requis'
    return
  }
  try {
    await createAdmin({ email: email.value })
    message.value = 'Utilisateur admin créé et email de réinitialisation envoyé.'
    email.value = ''
  }
  catch (e: unknown) {
    error.value = 'Erreur lors de la création'
  }
}
</script>

<template>
  <div class="grid">
    <div class="col-12">
      <ConfirmDialog />
      <h2>Créer un utilisateur Admin</h2>
      <form class="form" @submit.prevent="submit">
        <label for="email">Email</label>
        <InputText
          id="email"
          v-model="email"
          type="email"
          placeholder="admin@example.com"
          required
        />

        <Button
          type="submit"
          :label="isPending ? 'Création…' : 'Créer'"
          :disabled="isPending"
          :loading="isPending"
        />
      </form>

      <Message v-if="message" severity="success" class="mb-2">
        {{ message }}
      </Message>
      <Message v-if="error" severity="error" class="mb-2">
        {{ error }}
      </Message>

      <h3>Admins</h3>
      <div v-if="isLoading" class="loading">
        <ProgressSpinner style="width: 32px; height: 32px" stroke-width="6" />
      </div>
      <DataTable
        v-else
        :value="admins?.member ?? []"
        data-key="id"
        fluid
      >
        <Column header="ID">
          <template #body="{ data }">
            {{ data.id || data['@id'] }}
          </template>
        </Column>
        <Column field="username" header="Identifiant de connexion" />
        <Column field="email" header="Email" />
        <Column header="Actions">
          <template #body="{ data }">
            <Button
              label="Supprimer"
              severity="danger"
              @click="onRemove(data)"
            />
          </template>
        </Column>

        <template #empty>
          Aucun administrateur
        </template>
      </DataTable>
    </div>
  </div>
</template>

<style scoped lang="scss">
.admin-create {
  max-width: 640px;
}

.form {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.loading {
  display: flex;
  align-items: center;
  padding: 0.5rem 0;
}

.hint {
  margin-top: 1rem;
  color: #888;
}
</style>
