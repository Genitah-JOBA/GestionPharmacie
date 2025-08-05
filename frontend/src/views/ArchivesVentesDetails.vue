<template>
  <div class="p-6 dark:bg-gray-800 text-white">
    <h1 class="text-2xl font-bold mb-4">
      Détails des achats de {{ nomClient }}
    </h1>

    <div
      v-for="vente in ventes"
      :key="vente.date"
      class="mb-6 border-b pb-4"
    >
      <h2 class="text-xl font-semibold mb-2">📅 {{ vente.date }}</h2>

      <ul class="mb-2">
        <li
          v-for="produit in vente.produits"
          :key="produit.nom"
          class="ml-4"
        >
          - {{ produit.nom }} ({{ produit.quantite }}) :
          {{ produit.montant.toLocaleString() }} Ar
        </li>
      </ul>

      <p class="font-semibold dark:text-gray-400">
        Sous-total : {{ vente.sous_total.toLocaleString() }} Ar
      </p>

      <p class="italic dark:text-gray-400" v-if="vente.mode_paiement === 'Paiement en attente'">
        Montant payé partiellement : {{ (vente.montant_partiel || 0).toLocaleString() }} Ar
      </p>

      <p v-else class="italic dark:text-gray-400">
        Mode de paiement : {{ vente.mode_paiement }}
      </p>

      <p class="italic dark:text-gray-400" v-if="vente.mode_paiement === 'Paiement en attente'">
        Reste : {{
          Math.max(vente.sous_total - (vente.montant_partiel || 0), 0).toLocaleString()
        }} Ar
      </p>

      <button
        v-if="vente.mode_paiement === 'Paiement en attente'"
        @click="ouvrirModalePaiement(vente)"
        class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-green-700"
      >
        Effectuer le paiement
      </button>
    </div>

    <h2 class="text-xl font-bold mt-6">
      Total : {{ total.toLocaleString() }} Ar
    </h2>

    <div
      v-if="modaleVisible"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Pour effectuer le paiement.</h2>
        <p class="mb-4">Choisissez une option :</p>
        <button
          @click="ouvrirModalePartiel"
          class="block w-full mb-2 px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600"
        >
          Payer partiellement
        </button>
        <button
          @click="payerComplet"
          class="block w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Paiement complet
        </button>
        <button
          @click="fermerModales"
          class="mt-4 text-sm text-gray-600 underline"
        >
          Annuler
        </button>
      </div>
    </div>

    <div
      v-if="modalePartielleVisible"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Paiement partiel</h2>
        <label class="block mb-2">Montant :</label>
        <input
          type="number"
          v-model="montantPartiel"
          class="w-full border border-gray-300 p-2 mb-4 rounded"
        />
        <button
          @click="validerPaiementPartiel"
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Valider
        </button>
        <button
          @click="fermerModales"
          class="mt-4 text-sm text-gray-600 underline block"
        >
          Annuler
        </button>
      </div>
    </div>
  </div>
  <MessageModal
  :show="modal.show"
  :title="modal.title"
  :message="modal.message"
  :type="modal.type"
  :confirmText="modal.confirmText"
  :cancelText="modal.cancelText"
  @confirm="modal.confirm"
  @cancel="modal.cancel"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'
import { useModalStore } from '@/stores/ModalStore'
import MessageModal from '@/views/ModalMessage.vue'

const modal = useModalStore()

const route = useRoute()
const nomClient = ref(route.params.nomComplet)
const ventes = ref([])
const total = ref(0)
const idClient = ref(null)

const modaleVisible = ref(false)
const modalePartielleVisible = ref(false)
const venteSelectionnee = ref(null)
const montantPartiel = ref(null)

const fetchDetails = async () => {
  try {
    const response = await axios.get(`/archives/ventes/${nomClient.value}`)
    ventes.value = response.data.ventes
    total.value = response.data.total
    idClient.value = response.data.client_id
  } catch (error) {
    modal.open({
      title: 'Erreur',
      message: 'Impossible de charger les ventes',
      type: 'error'
    })
    console.error(error)
  }
}

onMounted(fetchDetails)

const ouvrirModalePaiement = (vente) => {
  venteSelectionnee.value = vente
  modaleVisible.value = true
}

const ouvrirModalePartiel = () => {
  modaleVisible.value = false
  modalePartielleVisible.value = true
}

const fermerModales = () => {
  modaleVisible.value = false
  modalePartielleVisible.value = false
  montantPartiel.value = null
}

const payerComplet = async () => {
  try {
    await axios.post(`/archives/ventes/${idClient.value}/payer-complet`, {
      date: venteSelectionnee.value.date,
    })
    venteSelectionnee.value.mode_paiement = 'Payé'
    fermerModales()
    modal.open({
      title: 'Succès',
      message: 'Paiement complet effectué avec succès.',
      type: 'success'
    })
  } catch (error) {
    modal.open({
      title: 'Erreur',
      message: 'Impossible de traiter le paiement complet.',
      type: 'error'
    })
    console.error(error)
  }
}

const validerPaiementPartiel = async () => {
  if (!montantPartiel.value) {
    modal.open({
      title: 'Attention',
      message: 'Veuillez saisir un montant.',
      type: 'warning'
    })
    return
  }

  try {
    await axios.post(`/archives/ventes/payer-partiel`, {
      vente_id: venteSelectionnee.value.vente_id,
      montant: montantPartiel.value,
    })

    venteSelectionnee.value.montant_partiel =
      (venteSelectionnee.value.montant_partiel || 0) + Number(montantPartiel.value)

    if (venteSelectionnee.value.montant_partiel >= venteSelectionnee.value.sous_total) {
      venteSelectionnee.value.mode_paiement = 'Payé'
    }

    fermerModales()
    modal.open({
      title: 'Succès',
      message: 'Paiement partiel enregistré avec succès.',
      type: 'success'
    })
  } catch (error) {
    modal.open({
      title: 'Erreur',
      message: 'Erreur lors de l\'enregistrement du paiement partiel.',
      type: 'error'
    })
    console.error(error)
  }
}
</script>

