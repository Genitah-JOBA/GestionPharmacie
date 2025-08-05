<template>
  <div class="vente-container animate-fade-in bg-gray-50 dark:bg-gray-800 min-h-screen text-gray-900 dark:text-gray-200 p-6">
    <h1 class="text-2xl font-bold mb-4 text-center">Gestion des Ventes</h1>

    <div class="flex justify-end mb-4">
      <input
        type="text"
        v-model="recherche"
        placeholder="🔍 Rechercher un médicament..."
        class="border border-gray-300 dark:border-gray-600 rounded p-2 w-1/2 shadow-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"
      />
    </div>

    <ul class="space-y-2">
      <li
        v-for="med in medicamentsFiltres"
        :key="med.reference"
        class="flex justify-between items-center p-3 bg-white dark:bg-gray-700 rounded shadow hover:bg-green-50 dark:hover:bg-gray-600 transition"
      >
        <div>
          <span class="font-semibold">{{ med.nom }}</span> - {{ med.dosage }} - Stock: {{ med.quantite }} - Prix: {{ med.prix_unitaire }} Ar
        </div>
        <button
          @click="ajouterAuPanier(med)"
          class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition dark:bg-green-900 dark:hover:bg-green-500"
        >
          Ajouter
        </button>
      </li>
    </ul>

    <h2 class="text-xl font-semibold mt-10 mb-2">Achat</h2>
    <table v-if="panier.length" class="w-full bg-white dark:bg-gray-800 shadow-md rounded animate-slide-up">
      <thead class="bg-green-100 dark:bg-green-900 text-gray-800 dark:text-gray-200">
        <tr>
          <th>Nom</th>
          <th>Dosage</th>
          <th>Quantité</th>
          <th>Prix unitaire</th>
          <th>Sous-total</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, index) in panier"
          :key="item.medicament.reference"
          class="text-center border-b border-gray-200 dark:border-gray-700"
        >
          <td>{{ item.medicament.nom }}</td>
          <td>{{ item.medicament.dosage }}</td>
          <td>
            <input
              type="number"
              v-model.number="item.quantite"
              @change="majQuantite(index)"
              min="1"
              :max="item.medicament.quantite"
              class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-20 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"
            />
          </td>
          <td>{{ item.medicament.prix_unitaire }}</td>
          <td>{{ (item.medicament.prix_unitaire * item.quantite).toFixed(2) }}</td>
          <td>
            <button
              @click="retirerDuPanier(index)"
              class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition"
            >
              Supprimer
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else class="text-gray-500 dark:text-gray-400 italic">Aucun médicament dans l'achat pour le moment.</p>

    <h3 class="text-right text-xl mt-4 font-semibold">Total : {{ total.toFixed(2) }} Ar</h3>

    <div
      v-if="panier.length"
      class="bg-gray-100 dark:bg-gray-800 p-4 mt-6 rounded shadow animate-fade-in"
    >
      <label class="block mb-2 font-medium">Nom du client :</label>
      <input
        v-model="nomClient"
        type="text"
        class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 w-full mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"
      />

      <label class="block mb-2 font-medium">Méthode de paiement :</label>
      <select
        v-model="modePaiement"
        class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 w-full mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200"
      >
        <option value="">--Choisir--</option>
        <option value="Espèces">Espèces</option>
        <option value="Mobile Money">Mobile Money</option>
        <option value="Virement">Virement</option>
        <option value="Paiement en attente">Paiement en attente</option>
      </select>

      <button
        @click="validerVente"
        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition dark:bg-green-900 dark:hover:bg-green-500"
      >
        Valider la vente
      </button>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useModalStore } from '@/stores/ModalStore'
import MessageModal from '@/views/ModalMessage.vue'

const recherche = ref('')
const medicaments = ref([])
const panier = ref([])
const nomClient = ref('')
const modePaiement = ref('')
const total = computed(() => panier.value.reduce((acc, item) => acc + item.quantite * item.medicament.prix_unitaire, 0))
const router = useRouter()
const modal = useModalStore()

const medicamentsFiltres = computed(() => {
  if (!recherche.value.trim()) return medicaments.value
  const term = recherche.value.toLowerCase()
  return medicaments.value.filter(m => m.nom.toLowerCase().includes(term))
})

function chargerMedicaments() {
  fetch('http://localhost:8000/api/medicaments', { credentials: 'include' })
    .then(res => res.json())
    .then(data => medicaments.value = data)
}

function ajouterAuPanier(med) {
  const exist = panier.value.find(item => item.medicament.reference === med.reference)
  if (exist) {
    if (exist.quantite < med.quantite) exist.quantite++
    else modal.open({ title: 'Stock insuffisant', message: 'Quantité max atteinte.', type: 'warning' })
  } else {
    panier.value.push({ medicament: med, quantite: 1 })
  }
}

function majQuantite(index) {
  let item = panier.value[index]
  if (item.quantite < 1) item.quantite = 1
  if (item.quantite > item.medicament.quantite) {
    modal.open({ title: 'Stock insuffisant', message: 'Quantité max atteinte.', type: 'warning' })
    item.quantite = item.medicament.quantite
  }
}

function retirerDuPanier(index) {
  panier.value.splice(index, 1)
}

async function validerVente() {
  if (!nomClient.value.trim() || !modePaiement.value) {
    modal.open({ title: 'Champs requis', message: 'Veuillez remplir tous les champs.', type: 'error' })
    return
  }

  try {
    await fetch("http://localhost:8000/sanctum/csrf-cookie", {
      credentials: "include",
    })

    const response = await fetch("http://localhost:8000/api/ventes", {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        client_nom: nomClient.value,
        mode_paiement: modePaiement.value,
        panier: panier.value.map((p) => ({
          reference: p.medicament.reference,
          quantite: p.quantite,
        })),
      }),
    })

    const data = await response.json()

    if (!response.ok) {
      modal.open({ title: 'Erreur', message: data.message || "Échec de la validation.", type: 'error' })
      return
    }

    modal.open({
      title: 'Succès',
      message: '✅ Vente enregistrée avec succès !',
      type: 'success',
      confirmText: 'OK',
      onConfirm: () => {
        router.push(`/facture/${data.vente_id}`)
      }
    })

    panier.value = []
    nomClient.value = ""
    modePaiement.value = ""
  } catch (error) {
    console.error("Erreur lors de la validation de la vente :", error)
    modal.open({ title: 'Erreur réseau', message: '❌ Une erreur est survenue. Vérifiez votre connexion.', type: 'error' })
  }
}

onMounted(() => {
  chargerMedicaments()
})
</script>

<style scoped>
h1{
  font-family: ENGRAVERS MT;
  font-weight: 50;
}
@keyframes fade-in {
  0% { opacity: 0; transform: translateY(10px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fade-in 0.5s ease-in-out;
}

@keyframes slide-up {
  0% { transform: translateY(30px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}
.animate-slide-up {
  animation: slide-up 0.6s ease-out;
}
</style>
